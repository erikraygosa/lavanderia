<?php

namespace App\Livewire\Fidelizacion;

use App\Models\Cliente;
use App\Models\WhatsappFidelizacionLog;
use App\Services\FidelizacionService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class ClientesFidelizacion extends Component
{
    use WithPagination;

    public string $buscar       = '';
    public string $filtroEstado = '';
    public string $tab          = 'clientes';

    protected $queryString = ['buscar', 'filtroEstado', 'tab'];

    public function updatingBuscar(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    /**
     * Alterna el filtro de estado; un segundo clic lo limpia.
     */
    public function filtrarPor(string $estado): void
    {
        $this->filtroEstado = $this->filtroEstado === $estado ? '' : $estado;
        $this->resetPage();
    }

    public function render()
    {
        $service = app(FidelizacionService::class);

        // Cargar todos los clientes activos con pedidos completados e ítems
        $clientes = Cliente::where('activo', true)
            ->with([
                'pedidos' => fn($q) => $q
                    ->whereIn('estado', ['terminado', 'entregado', 'pagado'])
                    ->with('items'),
            ])
            ->orderBy('nombre')
            ->get();

        // Clasificar en memoria
        $clasificados = $clientes->map(fn($c) => $service->clasificarCliente($c));

        // Conteos para las cards métricas
        $conteos = [
            'NUEVO'     => $clasificados->where('estado', 'NUEVO')->count(),
            'ACTIVO'    => $clasificados->where('estado', 'ACTIVO')->count(),
            'EN_RIESGO' => $clasificados->where('estado', 'EN_RIESGO')->count(),
            'INACTIVO'  => $clasificados->where('estado', 'INACTIVO')->count(),
        ];

        // Filtrar y ordenar
        $filtrados = $clasificados
            ->when(
                $this->filtroEstado,
                fn($col) => $col->where('estado', $this->filtroEstado)
            )
            ->when(
                $this->buscar,
                fn($col) => $col->filter(
                    fn($item) =>
                        mb_stripos($item['cliente']->nombre, $this->buscar) !== false ||
                        mb_stripos($item['cliente']->telefono ?? '', $this->buscar) !== false
                )
            )
            ->sortByDesc('dias_sin_venir')
            ->values();

        // Paginación manual
        $perPage     = 20;
        $currentPage = $this->getPage();
        $paginator   = new LengthAwarePaginator(
            $filtrados->forPage($currentPage, $perPage),
            $filtrados->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Historial solo cuando la pestaña está activa
        $historial = collect();
        if ($this->tab === 'historial') {
            $historial = WhatsappFidelizacionLog::with('cliente')
                ->orderByDesc('created_at')
                ->limit(50)
                ->get();
        }

        return view('livewire.fidelizacion.clientes-fidelizacion', compact(
            'conteos',
            'paginator',
            'historial'
        ))->layout('layouts.app', ['title' => 'Fidelización de Clientes']);
    }
}
