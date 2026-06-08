<?php

namespace App\Livewire\Fidelizacion;

use App\Models\Cliente;
use App\Services\EvolutionWhatsAppService;
use App\Services\FidelizacionService;
use Livewire\Component;

class MensajeCliente extends Component
{
    public Cliente $cliente;

    public array  $clasificacion   = [];
    public string $mensajeEditable = '';
    public bool   $enviando        = false;
    public ?string $alertaTipo     = null;
    public string  $alertaMensaje  = '';

    public function mount(Cliente $cliente): void
    {
        $cliente->loadMissing([
            'pedidos' => fn($q) => $q
                ->whereIn('estado', ['terminado', 'entregado', 'pagado'])
                ->with('items'),
        ]);

        $datos = app(FidelizacionService::class)->clasificarCliente($cliente);

        // Guardar solo escalares para que Livewire los serialice sin problema
        $this->clasificacion = [
            'estado'               => $datos['estado'],
            'color'                => $datos['color'],
            'total_pedidos'        => $datos['total_pedidos'],
            'ultimo_pedido_str'    => $datos['ultimo_pedido']?->toIso8601String(),
            'dias_sin_venir'       => $datos['dias_sin_venir'],
            'ticket_promedio'      => $datos['ticket_promedio'],
            'servicios_frecuentes' => $datos['servicios_frecuentes'],
        ];

        $this->mensajeEditable = $datos['mensaje_sugerido'];
    }

    public function enviarWhatsApp(): void
    {
        if ($this->enviando) {
            return;
        }

        if (!$this->cliente->telefono) {
            $this->alertaTipo    = 'error';
            $this->alertaMensaje = '❌ El cliente no tiene teléfono registrado.';
            return;
        }

        $this->enviando   = true;
        $this->alertaTipo = null;

        $resultado = app(EvolutionWhatsAppService::class)->enviarMensaje(
            $this->cliente->telefono,
            $this->mensajeEditable,
            $this->cliente->id,
            $this->clasificacion['estado']
        );

        $this->enviando = false;

        if ($resultado['success']) {
            $this->alertaTipo    = 'success';
            $this->alertaMensaje = "✅ Mensaje enviado correctamente a {$this->cliente->nombre}";
        } else {
            $this->alertaTipo    = 'error';
            $this->alertaMensaje = "❌ Error al enviar: {$resultado['error']}";
        }
    }

    public function abrirWhatsAppWeb(): void
    {
        if (!$this->cliente->telefono) {
            return;
        }

        $tel = preg_replace('/\D/', '', $this->cliente->telefono);
        if (strlen($tel) === 10) {
            $tel = '52' . $tel;
        }

        $url = 'https://wa.me/' . $tel . '?text=' . rawurlencode($this->mensajeEditable);
        $this->dispatch('abrirUrl', url: $url);
    }

    public function render()
    {
        return view('livewire.fidelizacion.mensaje-cliente')
            ->layout('layouts.app', ['title' => 'Mensaje — ' . $this->cliente->nombre]);
    }
}
