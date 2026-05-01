<?php

namespace App\Services;

use App\Models\Configuracion;
use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    private string $baseUrl;
    private string $instancia;
    private string $apiKey;
    public  string $ultimoError = '';

    public function __construct()
    {
        $this->baseUrl   = rtrim(Configuracion::obtener('evo_url', ''), '/');
        $this->instancia = Configuracion::obtener('evo_instancia', '');
        $this->apiKey    = Configuracion::obtener('evo_apikey', '');
    }

    public function estaConfigurado(): bool
    {
        return (bool) ($this->baseUrl && $this->instancia && $this->apiKey);
    }

    private function normalizarTelefono(string $telefono): string
    {
        $tel = preg_replace('/\D/', '', $telefono);
        // Si tiene 10 dígitos (MX sin código país) agrega 52
        if (strlen($tel) === 10) {
            $tel = '52' . $tel;
        }
        return $tel;
    }

    private function httpClient()
    {
        return Http::withHeaders([
            'apikey'       => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->withoutVerifying()   // soporta certificados auto-firmados en instancias self-hosted
          ->timeout(20);
    }

    public function enviarMensaje(string $telefono, string $mensaje): bool
    {
        if (!$this->estaConfigurado()) {
            $this->ultimoError = 'EvoAPI no está configurado.';
            return false;
        }

        $tel = $this->normalizarTelefono($telefono);

        try {
            $response = $this->httpClient()
                ->post("{$this->baseUrl}/message/sendText/{$this->instancia}", [
                    'number'  => $tel,
                    'text'    => $mensaje,
                    'options' => ['delay' => 1000, 'presence' => 'composing'],
                ]);

            if (!$response->successful()) {
                $this->ultimoError = "HTTP {$response->status()}: " . $response->body();
                Log::warning('EvoAPI error', ['status' => $response->status(), 'body' => $response->body()]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            $this->ultimoError = $e->getMessage();
            Log::error('EvoAPI excepción', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Envía el ticket del pedido como PDF adjunto + mensaje de texto.
     * Usa el endpoint sendMedia de EvoAPI.
     */
    public function enviarTicket(Pedido $pedido): bool
    {
        $pedido->loadMissing(['items', 'cliente']);

        $telefono = $pedido->cliente->telefono;
        if (!$telefono) {
            $this->ultimoError = 'El cliente no tiene teléfono registrado.';
            return false;
        }

        if (!$this->estaConfigurado()) {
            $this->ultimoError = 'EvoAPI no está configurado.';
            return false;
        }

        $tel = $this->normalizarTelefono($telefono);

        // 1 — Generar PDF del ticket
        $pdf    = Pdf::setPaper([0, 0, 226.77, 841.89]) // 80mm de ancho
                     ->loadView('pedidos.ticket-pdf', compact('pedido'));
        $base64 = base64_encode($pdf->output());

        // 2 — Construir caption/texto que acompaña el PDF
        $caption = $this->textoTicket($pedido);

        try {
            // EvoAPI requiere mediatype en el nivel raíz del body
            $response = $this->httpClient()
                ->post("{$this->baseUrl}/message/sendMedia/{$this->instancia}", [
                    'number'    => $tel,
                    'mediatype' => 'document',
                    'media'     => $base64,
                    'fileName'  => "ticket-{$pedido->folio}.pdf",
                    'caption'   => '',
                    'options'   => ['delay' => 1000],
                ]);

            if ($response->successful()) {
                return true;
            }

            $this->ultimoError = "HTTP {$response->status()}: " . $response->body();
            Log::warning('EvoAPI sendMedia falló', ['status' => $response->status(), 'body' => $response->body()]);
            return false;

        } catch (\Exception $e) {
            $this->ultimoError = $e->getMessage();
            Log::error('EvoAPI enviarTicket excepción', ['msg' => $e->getMessage()]);
            // fallback a texto
            return $this->enviarMensaje($telefono, $caption);
        }
    }

    /**
     * Notifica al cliente que su pedido está listo.
     * Acepta fecha y hora editadas desde el formulario del pedido.
     */
    public function notificarListo(Pedido $pedido, string $fecha = '', string $hora = ''): bool
    {
        $pedido->loadMissing(['cliente']);

        $telefono = $pedido->cliente->telefono;
        if (!$telefono) {
            $this->ultimoError = 'El cliente no tiene teléfono registrado.';
            return false;
        }

        if (!$this->estaConfigurado()) {
            $this->ultimoError = 'EvoAPI no está configurado.';
            return false;
        }

        $negocio  = Configuracion::obtener('negocio_nombre', 'Lavandería');
        $horarios = Configuracion::obtener('horarios_trabajo', '');
        $nombre   = $pedido->cliente->nombre;
        $folio    = $pedido->folio;

        // Construir la fecha/hora de recogida con los valores editados
        if ($fecha) {
            // ->locale('es') forzado para garantizar español sin depender del global
            $dt = \Carbon\Carbon::parse($fecha)->locale('es');
            $fechaStr = $dt->isoFormat('dddd D [de] MMMM');
            if ($hora) {
                $fechaStr .= ' a las ' . substr($hora, 0, 5) . ' hrs';
            }
        } else {
            $fechaStr = $pedido->entregaFormateada();
        }

        if ($pedido->es_domicilio) {
            $msg = "🧺 *{$negocio}*\n\n"
                 . "Hola *{$nombre}*, su pedido *{$folio}* está listo y en camino a su domicilio. 🛵\n\n"
                 . "📅 *Entrega estimada:* {$fechaStr}\n\n"
                 . "💵 Total: *\$" . number_format($pedido->total, 2) . "*";
        } else {
            $msg = "🧺 *{$negocio}*\n\n"
                 . "Hola *{$nombre}*, su pedido *{$folio}* está listo para recoger. ✅\n\n"
                 . "📅 *Puede pasar desde:* {$fechaStr}\n\n"
                 . "💵 Total a pagar: *\$" . number_format($pedido->total, 2) . "*";
        }

        // Agregar horarios de atención si están configurados
        if ($horarios) {
            $msg .= "\n\n🕐 *Nuestros horarios:*\n" . $horarios;
        }

        $msg .= "\n\n¡Le esperamos! 🙏";

        return $this->enviarMensaje($telefono, $msg);
    }

    private function textoTicket(Pedido $pedido): string
    {
        $negocio = Configuracion::obtener('negocio_nombre', 'Lavandería');
        $lineas  = [];

        $lineas[] = "🧺 *{$negocio}*";
        $lineas[] = "━━━━━━━━━━━━━━━━━━━━";
        $lineas[] = "📋 *Folio:* {$pedido->folio}";
        $lineas[] = "👤 *Cliente:* {$pedido->cliente->nombre}";
        $lineas[] = "📅 *Fecha:* " . $pedido->created_at->format('d/m/Y H:i');

        if ($pedido->fecha_entrega) {
            $entrega = $pedido->fecha_entrega->format('d/m/Y');
            if ($pedido->hora_entrega) {
                $entrega .= ' ' . substr($pedido->hora_entrega, 0, 5) . ' hrs';
            }
            $lineas[] = "🕐 *Entrega:* {$entrega}";
        }

        $lineas[] = '';
        $lineas[] = '*Detalle:*';
        $lineas[] = '─────────────────────';

        foreach ($pedido->items as $item) {
            $cant     = $item->cantidad + 0;
            $lineas[] = "• {$item->descripcion}";
            $lineas[] = "  {$cant} × \$" . number_format($item->precio_unitario, 2) . " = \$" . number_format($item->subtotal, 2);
        }

        $lineas[] = '─────────────────────';
        if ($pedido->descuento > 0) {
            $lineas[] = "Subtotal: \$" . number_format($pedido->subtotal, 2);
            $lineas[] = "Descuento: -\$" . number_format($pedido->descuento, 2);
        }
        $lineas[] = "💵 *TOTAL: \$" . number_format($pedido->total, 2) . "*";
        $lineas[] = '';

        if ($pedido->estado === 'pagado') {
            $lineas[] = "✅ *PAGADO* — " . ucfirst($pedido->metodo_pago ?? '');
        } else {
            $lineas[] = '⏳ *Pendiente de pago*';
        }

        if ($pedido->notas) {
            $lineas[] = '';
            $lineas[] = "📝 {$pedido->notas}";
        }

        $lineas[] = '';
        $lineas[] = '¡Gracias por su preferencia! 🙏';

        return implode("\n", $lineas);
    }
}

