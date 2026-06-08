<?php

namespace App\Services;

use App\Models\Configuracion;
use App\Models\WhatsappFidelizacionLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionWhatsAppService
{
    private string $baseUrl;
    private string $instancia;
    private string $apiKey;

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

    /**
     * Envía un mensaje de texto y registra el intento en whatsapp_fidelizacion_log.
     *
     * @return array{success: bool, error: string|null}
     */
    public function enviarMensaje(
        string $telefono,
        string $mensaje,
        int    $clienteId,
        string $estadoCliente
    ): array {
        $success = false;
        $error   = null;

        if (!$this->estaConfigurado()) {
            $error = 'Evolution API no está configurada. Revisa los valores en Configuración.';
            $this->registrar($clienteId, $telefono, $estadoCliente, $mensaje, $error);
            return compact('success', 'error');
        }

        $tel = $this->normalizarTelefono($telefono);

        try {
            $response = Http::withHeaders([
                'apikey'       => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(20)
            ->post("{$this->baseUrl}/message/sendText/{$this->instancia}", [
                'number'  => $tel,
                'text'    => $mensaje,
                'options' => ['delay' => 1000, 'presence' => 'composing'],
            ]);

            if ($response->successful()) {
                $success = true;
            } else {
                $error = "HTTP {$response->status()}: {$response->body()}";
                Log::warning('EvolutionWhatsAppService: error al enviar', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'cliente_id' => $clienteId,
                ]);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error('EvolutionWhatsAppService: excepción', [
                'msg'        => $e->getMessage(),
                'cliente_id' => $clienteId,
            ]);
        }

        $this->registrar($clienteId, $telefono, $estadoCliente, $mensaje, $success ? 'OK' : $error);

        return compact('success', 'error');
    }

    /**
     * Limpia el teléfono y agrega código de país MX si es número local de 10 dígitos.
     */
    private function normalizarTelefono(string $telefono): string
    {
        $tel = preg_replace('/\D/', '', $telefono);

        if (strlen($tel) === 10) {
            $tel = '52' . $tel;
        }

        return $tel;
    }

    /**
     * Registra el intento de envío en la tabla de auditoría.
     */
    private function registrar(
        int    $clienteId,
        string $telefono,
        string $estadoCliente,
        string $mensaje,
        ?string $respuesta
    ): void {
        try {
            WhatsappFidelizacionLog::create([
                'cliente_id'    => $clienteId,
                'telefono'      => $telefono,
                'estado_cliente'=> $estadoCliente,
                'mensaje_enviado'=> $mensaje,
                'respuesta_evo' => $respuesta,
                'enviado_at'    => Carbon::now('America/Merida'),
            ]);
        } catch (\Exception $e) {
            Log::error('EvolutionWhatsAppService: no se pudo guardar en log', [
                'msg' => $e->getMessage(),
            ]);
        }
    }
}
