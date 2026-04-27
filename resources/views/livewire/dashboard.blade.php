<div>
    @php
        $logoPath   = \App\Models\Configuracion::obtener('logo_path', '');
        $negocioNom = \App\Models\Configuracion::obtener('negocio_nombre', 'Lavandería');
        $negocioDir = \App\Models\Configuracion::obtener('negocio_direccion', '');
    @endphp
    <div class="mb-6 flex items-center gap-4">
        @if($logoPath)
            <img src="{{ Storage::url($logoPath) }}" alt="Logo" class="h-14 w-14 object-contain border border-gray-200 rounded-xl bg-white p-1 shadow-sm" />
        @endif
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $negocioNom }}</h2>
            @if($negocioDir)
                <p class="text-sm text-gray-400">{{ $negocioDir }}</p>
            @else
                <p class="text-sm text-gray-500">Resumen de operaciones — {{ now()->isoFormat('dddd D [de] MMMM, YYYY') }}</p>
            @endif
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Ventas hoy</p>
            <p class="text-2xl font-bold text-indigo-700">${{ number_format($this->ventasHoy, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $this->pedidosPagadosHoy }} pedido(s) cobrado(s)</p>
        </div>
        <div class="card">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Ventas esta semana</p>
            <p class="text-2xl font-bold text-indigo-700">${{ number_format($this->ventasSemana, 2) }}</p>
        </div>
        <div class="card">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Ventas este mes</p>
            <p class="text-2xl font-bold text-indigo-700">${{ number_format($this->ventasMes, 2) }}</p>
        </div>
    </div>

    {{-- Alertas y estado --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card border-l-4 border-l-yellow-400">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Pendientes de cobro</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $this->pedidosPendientes }}</p>
            <a href="{{ route('pedidos.index', ['estado' => 'pendiente']) }}" class="text-xs text-indigo-600 hover:underline mt-1 inline-block">Ver pedidos</a>
        </div>
        <div class="card border-l-4 {{ $this->pedidosVencidos > 0 ? 'border-l-red-400' : 'border-l-gray-200' }}">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Pedidos vencidos</p>
            <p class="text-3xl font-bold {{ $this->pedidosVencidos > 0 ? 'text-red-600' : 'text-gray-400' }}">
                {{ $this->pedidosVencidos }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Fecha de entrega pasada</p>
        </div>
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Accesos rápidos</p>
                    <div class="space-y-2 mt-2">
                        <a href="{{ route('pedidos.crear') }}" class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Nuevo pedido
                        </a>
                        <a href="{{ route('clientes.crear') }}" class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Nuevo cliente
                        </a>
                        <a href="{{ route('cortes.crear') }}" class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Nuevo corte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfica + últimos pedidos --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Gráfica ventas 7 días --}}
        <div class="lg:col-span-2 card">
            <h3 class="font-medium text-gray-900 mb-4">Ventas últimos 7 días</h3>
            <div style="height:200px; position:relative;">
                <canvas id="graficaVentas"></canvas>
            </div>
        </div>

        {{-- Últimos pedidos --}}
        <div class="card">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-medium text-gray-900">Últimos pedidos</h3>
                <a href="{{ route('pedidos.index') }}" class="text-xs text-indigo-600 hover:underline">Ver todos</a>
            </div>
            <div class="space-y-2">
                @forelse($this->ultimosPedidos as $pedido)
                    @php $badge = $pedido->estadoBadge(); @endphp
                    <a href="{{ route('pedidos.show', $pedido) }}" class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <div>
                            <p class="text-xs font-mono font-medium text-gray-900">{{ $pedido->folio }}</p>
                            <p class="text-xs text-gray-500">{{ Str::limit($pedido->cliente->nombre, 18) }}</p>
                        </div>
                        <div class="text-right">
                            <span class="badge-{{ $pedido->estado }} text-xs">{{ $badge['texto'] }}</span>
                            <p class="text-xs font-medium text-gray-700 mt-0.5">${{ number_format($pedido->total, 2) }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">Sin pedidos aún</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('graficaVentas');
    if (!ctx) return;

    const grafica = @json($this->graficaVentas);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: grafica.labels,
            datasets: [{
                label: 'Ventas ($)',
                data: grafica.values,
                backgroundColor: 'rgba(99, 102, 241, 0.7)',
                borderColor: 'rgb(99, 102, 241)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ' $' + ctx.parsed.y.toFixed(2)
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (v) => '$' + v
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush
