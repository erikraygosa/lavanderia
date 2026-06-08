<?php

namespace App\Livewire;

use App\Models\Pedido;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    // Estados que representan un pedido completado/cobrado
    private const ESTADOS_VENTAS = ['terminado', 'entregado', 'pagado'];

    public function getVentasHoyProperty(): float
    {
        return Pedido::whereIn('estado', self::ESTADOS_VENTAS)
            ->whereDate('created_at', Carbon::today('America/Merida'))
            ->sum('total');
    }

    public function getVentasSemanaProperty(): float
    {
        $ahora = Carbon::now('America/Merida');

        return Pedido::whereIn('estado', self::ESTADOS_VENTAS)
            ->whereBetween('created_at', [
                $ahora->copy()->startOfWeek(),
                $ahora->copy()->endOfWeek(),
            ])
            ->sum('total');
    }

    public function getVentasMesProperty(): float
    {
        $ahora = Carbon::now('America/Merida');

        return Pedido::whereIn('estado', self::ESTADOS_VENTAS)
            ->whereBetween('created_at', [
                $ahora->copy()->startOfMonth(),
                $ahora,
            ])
            ->sum('total');
    }

    public function getPedidosPendientesProperty(): int
    {
        return Pedido::where('estado', 'pendiente')->count();
    }

    public function getPedidosPagadosHoyProperty(): int
    {
        // Pedidos completados creados hoy (pagado_en puede ser null en algunos estados)
        return Pedido::whereIn('estado', self::ESTADOS_VENTAS)
            ->whereDate('created_at', Carbon::today('America/Merida'))
            ->count();
    }

    public function getPedidosVencidosProperty(): int
    {
        return Pedido::where('estado', 'pendiente')
            ->whereDate('fecha_entrega', '<', Carbon::today('America/Merida'))
            ->count();
    }

    public function getUltimosPedidosProperty()
    {
        return Pedido::with('cliente')->orderByDesc('id')->limit(6)->get();
    }

    public function getGraficaVentasProperty(): array
    {
        $ahora = Carbon::now('America/Merida');

        $datos = Pedido::whereIn('estado', self::ESTADOS_VENTAS)
            ->where('created_at', '>=', $ahora->copy()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(total) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->pluck('total', 'fecha');

        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha    = $ahora->copy()->subDays($i)->format('Y-m-d');
            $labels[] = $ahora->copy()->subDays($i)->isoFormat('ddd D');
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

