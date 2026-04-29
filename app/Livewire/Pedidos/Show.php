<?php

namespace App\Livewire\Pedidos;

use App\Models\Pedido;
use App\Services\WhatsappService;
use Livewire\Component;

class Show extends Component
{
    public Pedido $pedido;

    public string $metodoPago        = 'efectivo';
    public bool   $enviandoWhatsapp  = false;
    public string $mensajeWhatsapp   = '';

    public function mount(Pedido $pedido): void
    {
        $this->pedido     = $pedido->load('items', 'cliente');
        $this->metodoPago = $pedido->metodo_pago ?? 'efectivo';
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

    public function marcarPagado(): void
    {
        $this->pedido->update([
            'estado'      => 'pagado',
            'metodo_pago' => $this->metodoPago,
            'pagado_en'   => now(),
        ]);
        $this->pedido->refresh();
        session()->flash('exito', 'Pedido marcado como pagado.');
    }

    public function marcarPendiente(): void
    {
        $this->pedido->update([
            'estado'       => 'pendiente',
            'pagado_en'    => null,
            'terminado_en' => null,
            'entregado_en' => null,
        ]);
        $this->pedido->refresh();
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

    /** Notifica al cliente que su pedido está listo */
    public function notificarListo(): void
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

            $ok = $service->notificarListo($this->pedido);

            $this->mensajeWhatsapp = $ok
                ? '✅ Notificación enviada al cliente.'
                : '❌ Error al enviar: ' . $service->ultimoError;

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
