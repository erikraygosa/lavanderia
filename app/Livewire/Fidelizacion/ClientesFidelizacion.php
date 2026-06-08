<?php

namespace App\Livewire\Fidelizacion;

use App\Models\Cliente;
use App\Models\WhatsappFidelizacionLog;
use App\Services\EvolutionWhatsAppService;
use App\Services\FidelizacionService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class ClientesFidelizacion extends Component
{
    use WithPagination;

    public string $buscar        = '';
    public string $filtroEstado  = '';
    public string $tab           = 'clientes';

    // Estado del modal
    public bool   $modalAbierto  = false;
    public ?int   $clienteModalId = null;
    public array  $clienteModal  = [];
    public string $mensajeEditable = '';
    public bool   $enviando      = false;
    public ?string $alertaTipo   = null;  // 'success' | 'error'
    public string  $alertaMensaje = '';

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
     * Alterna el filtro de estado; un segundo clic en la misma card lo limpia.
     */
    public function filtrarPor(string $estado): void
    {
        $this->filtroEstado = $this->filtroEstado === $estado ? '' : $estado;
        $this->resetPage();
    }

    /**
     * Abre el modal de mensaje para el cliente indicado.
     * IMPORTANTE: $clienteModal solo guarda escalares (sin modelos Eloquent)
     * para que Livewire pueda serializarlo correctamente entre requests.
     */
    public function abrirModal(int $clienteId): void
    {
        $cliente = Cliente::with([
            // Solo contar pedidos completados para fidelización
            'pedidos' => fn($q) => $q
                ->whereIn('estado', ['terminado', 'entregado', 'pagado'])
                ->with('items'),
        ])->findOrFail($clienteId);

        $clasificacion = app(FidelizacionService::class)->clasificarCliente($cliente);

        // Almacenar solo valores escalares/array — los modelos Eloquent no sobreviven
        // la serialización JSON de Livewire y quedan como arrays planos en el snapshot
        $this->clienteModal = [
            'nombre'               => $cliente->nombre,
            'telefono'             => $cliente->telefono ?? '',
            'estado'               => $clasificacion['estado'],
            'color'                => $clasificacion['color'],
            'total_pedidos'        => $clasificacion['total_pedidos'],
            'ultimo_pedido_str'    => $clasificacion['ultimo_pedido']?->toIso8601String(),
            'dias_sin_venir'       => $clasificacion['dias_sin_venir'],
            'ticket_promedio'      => $clasificacion['ticket_promedio'],
            'servicios_frecuentes' => $clasificacion['servicios_frecuentes'],
            'mensaje_sugerido'     => $clasificacion['mensaje_sugerido'],
        ];

        $this->mensajeEditable = $clasificacion['mensaje_sugerido'];
        $this->clienteModalId  = $clienteId;
        $this->alertaTipo      = null;
        $this->alertaMensaje   = '';
        $this->enviando        = false;
        $this->modalAbierto    = true;
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto   = false;
        $this->clienteModalId = null;
        $this->clienteModal   = [];
        $this->alertaTipo     = null;
        $this->alertaMensaje  = '';
    }

    /**
     * Envía el mensaje via EvolutionWhatsAppService y registra el resultado.
     */
    public function enviarWhatsApp(): void
    {
        if (!$this->clienteModalId || $this->enviando) {
            return;
        }

        $this->enviando = true;
        $this->alertaTipo = null;

        $cliente = Cliente::find($this->clienteModalId);

        if (!$cliente || !$cliente->telefono) {
            $this->alertaTipo    = 'error';
            $this->alertaMensaje = '❌ El cliente no tiene teléfono registrado.';
            $this->enviando      = false;
            return;
        }

        $resultado = app(EvolutionWhatsAppService::class)->enviarMensaje(
            $cliente->telefono,
            $this->mensajeEditable,
            $this->clienteModalId,
            $this->clienteModal['estado'] ?? 'INACTIVO'
        );

        if ($resultado['success']) {
            $this->alertaTipo    = 'success';
            $this->alertaMensaje = "✅ Mensaje enviado correctamente a {$cliente->nombre}";
        } else {
            $this->alertaTipo    = 'error';
            $this->alertaMensaje = "❌ Error al enviar: {$resultado['error']}";
        }

        $this->enviando = false;

        // Señal para que Alpine bloquee el botón 3 segundos
        $this->dispatch('mensajeEnviado');
    }

    /**
     * Abre WhatsApp Web en nueva pestaña como fallback.
     */
    public function abrirWhatsAppWeb(): void
    {
        if (!$this->clienteModalId) {
            return;
        }

        $cliente = Cliente::find($this->clienteModalId);
        if (!$cliente?->telefono) {
            return;
        }

        $tel = preg_replace('/\D/', '', $cliente->telefono);
        if (strlen($tel) === 10) {
            $tel = '52' . $tel;
        }

        $url = 'https://wa.me/' . $tel . '?text=' . rawurlencode($this->mensajeEditable);
        $this->dispatch('abrirUrl', url: $url);
    }

    public function render()
    {
        $service = app(FidelizacionService::class);

        // Cargar todos los clientes activos con sus pedidos completados e ítems
        $clientes = Cliente::where('activo', true)
            ->with([
                'pedidos' => fn($q) => $q
                    ->whereIn('estado', ['terminado', 'entregado', 'pagado'])
                    ->with('items'),
            ])
            ->orderBy('nombre')
            ->get();

        // Clasificar en memoria (sin persistir)
        $clasificados = $clientes->map(fn($c) => $service->clasificarCliente($c));

        // Conteos para las cards métricas
        $conteos = [
            'NUEVO'     => $clasificados->where('estado', 'NUEVO')->count(),
            'ACTIVO'    => $clasificados->where('estado', 'ACTIVO')->count(),
            'EN_RIESGO' => $clasificados->where('estado', 'EN_RIESGO')->count(),
            'INACTIVO'  => $clasificados->where('estado', 'INACTIVO')->count(),
        ];

        // Filtrar por estado y búsqueda
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

        // Paginación manual sobre la colección filtrada/ordenada
        $perPage     = 20;
        $currentPage = $this->getPage();
        $items       = $filtrados->forPage($currentPage, $perPage);

        $paginator = new LengthAwarePaginator(
            $items,
            $filtrados->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Historial sólo cuando la pestaña está activa
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
