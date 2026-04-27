<?php

namespace App\Livewire;

use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function getVentasHoyProperty(): float
    {
        return Pedido::where('estado', 'pagado')
            ->whereDate('pagado_en', today())
            ->sum('total');
    }

    public function getVentasSemanaProperty(): float
    {
        return Pedido::where('estado', 'pagado')
            ->whereBetween('pagado_en', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total');
    }

    public function getVentasMesProperty(): float
    {
        return Pedido::where('estado', 'pagado')
            ->whereMonth('pagado_en', now()->month)
            ->whereYear('pagado_en', now()->year)
            ->sum('total');
    }

    public function getPedidosPendientesProperty(): int
    {
        return Pedido::where('estado', 'pendiente')->count();
    }

    public function getPedidosPagadosHoyProperty(): int
    {
        return Pedido::where('estado', 'pagado')->whereDate('pagado_en', today())->count();
    }

    public function getPedidosVencidosProperty(): int
    {
        return Pedido::where('estado', 'pendiente')
            ->whereDate('fecha_entrega', '<', today())
            ->count();
    }

    public function getUltimosPedidosProperty()
    {
        return Pedido::with('cliente')->orderByDesc('id')->limit(6)->get();
    }

    public function getGraficaVentasProperty(): array
    {
        $datos = Pedido::where('estado', 'pagado')
            ->where('pagado_en', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(pagado_en) as fecha'), DB::raw('SUM(total) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->pluck('total', 'fecha');

        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->isoFormat('ddd D');
            $values[] = (float) ($datos[$fecha] ?? 0);
        }

        return compact('labels', 'values');
    }

    public function mount(): void
    {
        if (!auth()->user()->tienePermiso('dashboard')) {
            $this->redirect(route('pedidos.index'), navigate: false);
        }
    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('layouts.app', ['title' => 'Dashboard']);
    }
}

