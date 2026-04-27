<?php

namespace App\Livewire\Cortes;

use App\Models\Corte;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Form extends Component
{
    public string $fechaInicio = '';
    public string $fechaFin    = '';
    public string $observaciones = '';

    // Calculados al previsualizar
    public ?array $preview = null;

    public function mount(): void
    {
        $this->fechaInicio = now()->startOfMonth()->format('Y-m-d');
        $this->fechaFin    = now()->format('Y-m-d');
    }

    public function calcular(): void
    {
        $this->validate([
            'fechaInicio' => 'required|date',
            'fechaFin'    => 'required|date|after_or_equal:fechaInicio',
        ], [
            'fechaInicio.required' => 'La fecha de inicio es obligatoria.',
            'fechaFin.required'    => 'La fecha de fin es obligatoria.',
            'fechaFin.after_or_equal' => 'La fecha de fin debe ser igual o posterior al inicio.',
        ]);

        $pedidos = Pedido::where('estado', 'pagado')
            ->whereBetween('pagado_en', [
                $this->fechaInicio . ' 00:00:00',
                $this->fechaFin    . ' 23:59:59',
            ])
            ->get();

        $this->preview = [
            'total_ventas'   => $pedidos->sum('total'),
            'total_pedidos'  => $pedidos->count(),
            'efectivo'       => $pedidos->where('metodo_pago', 'efectivo')->sum('total'),
            'tarjeta'        => $pedidos->where('metodo_pago', 'tarjeta')->sum('total'),
            'transferencia'  => $pedidos->where('metodo_pago', 'transferencia')->sum('total'),
            'otro'           => $pedidos->where('metodo_pago', 'otro')->sum('total'),
        ];
    }

    public function guardar(): void
    {
        if (!$this->preview) {
            $this->calcular();
        }

        $corte = Corte::create([
            'folio'         => Corte::generarFolio(),
            'user_id'       => Auth::id() ?? 1,
            'fecha_inicio'  => $this->fechaInicio,
            'fecha_fin'     => $this->fechaFin,
            'total_ventas'  => $this->preview['total_ventas'],
            'total_pedidos' => $this->preview['total_pedidos'],
            'efectivo'      => $this->preview['efectivo'],
            'tarjeta'       => $this->preview['tarjeta'],
            'transferencia' => $this->preview['transferencia'],
            'otro'          => $this->preview['otro'],
            'observaciones' => $this->observaciones,
            'cerrado_en'    => now(),
        ]);

        session()->flash('exito', 'Corte generado: ' . $corte->folio);
        $this->redirect(route('cortes.show', $corte));
    }

    public function render()
    {
        return view('livewire.cortes.form')
            ->layout('layouts.app', ['title' => 'Nuevo corte de caja']);
    }
}

