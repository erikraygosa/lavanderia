<?php

namespace App\Livewire\Cortes;

use App\Models\Corte;
use App\Models\PedidoPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    /** Ventas mensuales de los últimos 12 meses (desde pedido_pagos) */
    public function getVentasMensualesProperty(): array
    {
        $tz    = 'America/Merida';
        $ahora = Carbon::now($tz);

        $pagos = PedidoPago::select(
                DB::raw('YEAR(CONVERT_TZ(created_at, "+00:00", "-06:00"))  as anio'),
                DB::raw('MONTH(CONVERT_TZ(created_at, "+00:00", "-06:00")) as mes'),
                DB::raw('SUM(monto) as total'),
                DB::raw('COUNT(DISTINCT pedido_id) as pedidos')
            )
            ->where('created_at', '>=', $ahora->copy()->subMonths(11)->startOfMonth()->utc())
            ->groupBy('anio', 'mes')
            ->orderBy('anio')
            ->orderBy('mes')
            ->get()
            ->keyBy(fn($r) => $r->anio . '-' . str_pad($r->mes, 2, '0', STR_PAD_LEFT));

        $labels  = [];
        $totales = [];
        $pedidos = [];
        $fechas  = [];

        for ($i = 11; $i >= 0; $i--) {
            $fecha  = $ahora->copy()->subMonths($i);
            $key    = $fecha->format('Y-m');
            $labels[]  = $fecha->locale('es')->isoFormat('MMM YYYY');
            $totales[] = (float) ($pagos[$key]->total   ?? 0);
            $pedidos[] = (int)   ($pagos[$key]->pedidos ?? 0);
            $fechas[]  = [
                'desde' => $fecha->copy()->startOfMonth()->format('Y-m-d'),
                'hasta' => $fecha->copy()->endOfMonth()->format('Y-m-d'),
            ];
        }

        return compact('labels', 'totales', 'pedidos', 'fechas');
    }

    public function render()
    {
        $cortes = Corte::with('user')
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.cortes.index', compact('cortes'))
            ->layout('layouts.app', ['title' => 'Cortes de caja']);
    }
}

