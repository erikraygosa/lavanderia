<?php

namespace App\Livewire\Cortes;

use App\Models\Corte;
use App\Models\PedidoPago;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Form extends Component
{
    public string $fechaInicio   = '';
    public string $fechaFin      = '';
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
            'fechaInicio.required'    => 'La fecha de inicio es obligatoria.',
            'fechaFin.required'       => 'La fecha de fin es obligatoria.',
            'fechaFin.after_or_equal' => 'La fecha de fin debe ser igual o posterior al inicio.',
        ]);

        // Usar pedido_pagos para el dinero REAL recibido (incluye anticios y liquidaciones)
        $pagos = PedidoPago::whereBetween(
                DB::raw('DATE(CONVERT_TZ(created_at, "+00:00", "-06:00"))'),
                [$this->fechaInicio, $this->fechaFin]
            )
            ->select('metodo_pago', DB::raw('SUM(monto) as total'), DB::raw('COUNT(DISTINCT pedido_id) as pedidos'))
            ->groupBy('metodo_pago')
            ->get()
            ->keyBy('metodo_pago');

        $totalPedidos = PedidoPago::whereBetween(
                DB::raw('DATE(CONVERT_TZ(created_at, "+00:00", "-06:00"))'),
                [$this->fechaInicio, $this->fechaFin]
            )
            ->distinct('pedido_id')
            ->count('pedido_id');

        $totalVentas = $pagos->sum('total');

        $this->preview = [
            'total_ventas'   => $totalVentas,
            'total_pedidos'  => $totalPedidos,
            'efectivo'       => (float) ($pagos['efectivo']->total       ?? 0),
            'tarjeta'        => (float) ($pagos['tarjeta']->total        ?? 0),
            'transferencia'  => (float) ($pagos['transferencia']->total  ?? 0),
            'otro'           => (float) ($pagos->filter(fn($p) => !in_array($p->metodo_pago, ['efectivo','tarjeta','transferencia']))->sum('total')),
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

