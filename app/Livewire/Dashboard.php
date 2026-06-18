<?php

namespace App\Livewire;

use App\Models\Pedido;
use App\Models\PedidoPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    // Estados que representan un pedido completado/cobrado
    private const ESTADOS_VENTAS = ['terminado', 'entregado', 'pagado'];

    public string $vista = 'resumen'; // 'resumen' | 'flujo'

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

    /** Flujo de efectivo: dinero que ENTRÓ cada día (abonos + liquidaciones) */
    public function getFlujoDiarioProperty(): array
    {
        $tz   = 'America/Merida';
        $ahora = Carbon::now($tz);

        // Pagos de los últimos 14 días agrupados por día y método
        $pagos = PedidoPago::select(
                DB::raw('DATE(CONVERT_TZ(created_at, "+00:00", "-06:00")) as fecha'),
                'metodo_pago',
                DB::raw('SUM(monto) as total'),
                DB::raw('COUNT(*) as operaciones')
            )
            ->where('created_at', '>=', $ahora->copy()->subDays(13)->startOfDay()->utc())
            ->groupBy('fecha', 'metodo_pago')
            ->orderBy('fecha')
            ->get();

        // Construir array de los 14 días con totales
        $dias = [];
        for ($i = 13; $i >= 0; $i--) {
            $fecha = $ahora->copy()->subDays($i)->format('Y-m-d');
            $dias[$fecha] = [
                'label'       => $ahora->copy()->subDays($i)->locale('es')->isoFormat('ddd D MMM'),
                'total'       => 0,
                'efectivo'    => 0,
                'tarjeta'     => 0,
                'transferencia' => 0,
                'otro'        => 0,
                'operaciones' => 0,
            ];
        }

        foreach ($pagos as $p) {
            if (isset($dias[$p->fecha])) {
                $dias[$p->fecha]['total']       += (float) $p->total;
                $dias[$p->fecha]['operaciones'] += (int) $p->operaciones;
                $metodo = in_array($p->metodo_pago, ['efectivo','tarjeta','transferencia']) ? $p->metodo_pago : 'otro';
                $dias[$p->fecha][$metodo]       += (float) $p->total;
            }
        }

        return array_values($dias);
    }

    /** Totales de hoy en el flujo */
    public function getFlujohoyProperty(): array
    {
        $tz = 'America/Merida';
        $hoy = Carbon::today($tz);

        $pagos = PedidoPago::whereDate(
                DB::raw('CONVERT_TZ(created_at, "+00:00", "-06:00")'),
                $hoy->format('Y-m-d')
            )
            ->select('metodo_pago', DB::raw('SUM(monto) as total'))
            ->groupBy('metodo_pago')
            ->get()
            ->keyBy('metodo_pago');

        return [
            'total'         => PedidoPago::whereDate(DB::raw('CONVERT_TZ(created_at,"+00:00","-06:00")'), $hoy->format('Y-m-d'))->sum('monto'),
            'efectivo'      => $pagos['efectivo']->total ?? 0,
            'tarjeta'       => $pagos['tarjeta']->total ?? 0,
            'transferencia' => $pagos['transferencia']->total ?? 0,
        ];
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

