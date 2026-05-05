<?php

namespace App\Livewire\Pedidos;

use App\Models\Pedido;
use App\Services\WhatsappService;
use Livewire\Component;

class Show extends Component
{
    public Pedido $pedido;

    public string $metodoPago        = 'efectivo';
    public float  $montoAbono        = 0;
    public bool   $enviandoWhatsapp  = false;
    public string $mensajeWhatsapp   = '';

    // Formulario inline para registrar abono
    public bool  $mostrarFormAbono = false;

    // Formulario inline para notificar al cliente
    public bool   $mostrarFormNotificar = false;
    public string $notificarFecha       = '';
    public string $notificarHora        = '';

    public function mount(Pedido $pedido): void
    {
        $this->pedido     = $pedido->load('items', 'cliente');
        $this->metodoPago = $pedido->metodo_pago ?? 'efectivo';
        $this->montoAbono = $pedido->saldoPendiente();
    }

    /** Abre el formulario de abono con monto vacío */
    public function abrirFormAbono(): void
    {
        $this->montoAbono      = 0;
        $this->mostrarFormAbono = true;
    }

    /** Liquida el saldo completo de una vez */
    public function liquidarTodo(): void
    {
        $this->montoAbono = $this->pedido->saldoPendiente();
        $this->cobrar();
    }

    /** Abre el formulario pre-llenado con la fecha/hora de entrega del pedido */
    public function abrirFormNotificar(): void
    {
        $this->notificarFecha = $this->pedido->fecha_entrega
            ? $this->pedido->fecha_entrega->format('Y-m-d')
            : now()->format('Y-m-d');

        $this->notificarHora = $this->pedido->hora_entrega
            ? substr($this->pedido->hora_entrega, 0, 5)
            : now()->format('H:i');

        $this->mostrarFormNotificar = true;
    }

    // ── Cambios de estado ────────────────────────────────────────────────────

    public function marcarTerminado(): void
    {
        $this->pedido->update([
            'estado'       => 'terminado',
            'terminado_en' => now(),
        ]);
        $this->pedido->refresh();
        session()->flash('exito', 'Pedido marcado como listo.');
    }

    public function marcarEntregado(): void
    {
        $this->pedido->update([
            'estado'       => 'entregado',
            'entregado_en' => now(),
        ]);
        $this->pedido->refresh();
        session()->flash('exito', 'Pedido marcado como entregado.');
    }

    /**
     * Registra un cobro (parcial = abono, total = liquidación).
     * Si el monto cubre el saldo pendiente, el pedido pasa a "pagado".
     */
    public function cobrar(): void
    {
        $this->validate([
            'montoAbono' => 'required|numeric|min:0.01|max:' . ($this->pedido->saldoPendiente() + 0.001),
        ], [
            'montoAbono.required' => 'Ingresa un monto.',
            'montoAbono.numeric'  => 'Debe ser un número.',
            'montoAbono.min'      => 'El monto debe ser mayor a $0.',
            'montoAbono.max'      => 'El monto no puede superar el saldo pendiente.',
        ]);

        $nuevoAnticipo = (float) $this->pedido->anticipo + $this->montoAbono;
        $liquidado     = $nuevoAnticipo >= (float) $this->pedido->total;

        $datos = [
            'anticipo'        => $nuevoAnticipo,
            'anticipo_metodo' => $this->metodoPago,
        ];

        if ($liquidado) {
            $datos['estado']      = 'pagado';
            $datos['metodo_pago'] = $this->metodoPago;
            $datos['pagado_en']   = now();
            $mensaje = '✅ Pedido liquidado correctamente.';
        } else {
            $saldoRestante = $this->pedido->total - $nuevoAnticipo;
            $mensaje = '💰 Abono de $' . number_format($this->montoAbono, 2)
                     . ' registrado. Saldo: $' . number_format($saldoRestante, 2);
        }

        $this->pedido->update($datos);
        $this->pedido->refresh();
        $this->montoAbono       = $this->pedido->saldoPendiente();
        $this->mostrarFormAbono = false;
        session()->flash('exito', $mensaje);
    }

    /** Compatibilidad: liquidar directamente desde estado entregado */
    public function marcarPagado(): void
    {
        $this->pedido->update([
            'estado'      => 'pagado',
            'metodo_pago' => $this->metodoPago,
            'pagado_en'   => now(),
            'anticipo'    => $this->pedido->total,
        ]);
        $this->pedido->refresh();
        $this->montoAbono = 0;
        session()->flash('exito', 'Pedido marcado como pagado.');
    }

    public function marcarPendiente(): void
    {
        $this->pedido->update([
            'estado'          => 'pendiente',
            'pagado_en'       => null,
            'terminado_en'    => null,
            'entregado_en'    => null,
            'anticipo'        => 0,
            'anticipo_metodo' => null,
        ]);
        $this->pedido->refresh();
        $this->montoAbono = $this->pedido->saldoPendiente();
    }

    public function marcarAbandonado(): void
    {
        $this->pedido->update(['estado' => 'abandonado']);
        $this->pedido->refresh();
    }

    // ── WhatsApp ─────────────────────────────────────────────────────────────

    /** Envía el ticket PDF por WhatsApp */
    public function enviarWhatsapp(): void
    {
        $this->mensajeWhatsapp = '';

        if (!$this->pedido->cliente->telefono) {
            $this->mensajeWhatsapp = '⚠️ El cliente no tiene teléfono registrado.';
            return;
        }

        $this->enviandoWhatsapp = true;

        try {
            $service = app(WhatsappService::class);

            if (!$service->estaConfigurado()) {
                $this->mensajeWhatsapp = '❌ EvoAPI no está configurado. Ve a Configuración.';
                $this->enviandoWhatsapp = false;
                return;
            }

            $ok = $service->enviarTicket($this->pedido);

            $this->mensajeWhatsapp = $ok
                ? '✅ Ticket enviado por WhatsApp correctamente.'
                : '❌ Error al enviar: ' . $service->ultimoError;

        } catch (\Exception $e) {
            $this->mensajeWhatsapp = '❌ Excepción: ' . $e->getMessage();
        }

        $this->enviandoWhatsapp = false;
    }

    /** Notifica al cliente que su pedido está listo (con fecha/hora editada) */
    public function notificarListo(): void
    {
        $this->mensajeWhatsapp = '';

        if (!$this->pedido->cliente->telefono) {
            $this->mensajeWhatsapp = '⚠️ El cliente no tiene teléfono registrado.';
            return;
        }

        $this->validate([
            'notificarFecha' => 'required|date',
            'notificarHora'  => 'required',
        ], [
            'notificarFecha.required' => 'La fecha es obligatoria.',
            'notificarFecha.date'     => 'Formato de fecha inválido.',
            'notificarHora.required'  => 'La hora es obligatoria.',
        ]);

        $this->enviandoWhatsapp = true;

        try {
            $service = app(WhatsappService::class);

            if (!$service->estaConfigurado()) {
                $this->mensajeWhatsapp = '❌ EvoAPI no está configurado. Ve a Configuración.';
                $this->enviandoWhatsapp = false;
                return;
            }

            $ok = $service->notificarListo(
                $this->pedido,
                $this->notificarFecha,
                $this->notificarHora
            );

            $this->mensajeWhatsapp = $ok
                ? '✅ Notificación enviada al cliente.'
                : '❌ Error al enviar: ' . $service->ultimoError;

            if ($ok) {
                $this->mostrarFormNotificar = false;
            }

        } catch (\Exception $e) {
            $this->mensajeWhatsapp = '❌ Excepción: ' . $e->getMessage();
        }

        $this->enviandoWhatsapp = false;
    }

    public function render()
    {
        return view('livewire.pedidos.show')
            ->layout('layouts.app', ['title' => 'Pedido ' . $this->pedido->folio]);
    }
}
