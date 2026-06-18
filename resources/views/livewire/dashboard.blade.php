<div>
    @php
        $logoPath   = \App\Models\Configuracion::obtener('logo_path', '');
        $negocioNom = \App\Models\Configuracion::obtener('negocio_nombre', 'Lavandería');
        $negocioDir = \App\Models\Configuracion::obtener('negocio_direccion', '');
    @endphp

    {{-- Header + toggle de vista --}}
    <div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-4">
            @if($logoPath)
                <img src="{{ Storage::url($logoPath) }}" alt="Logo"
                     class="h-14 w-14 object-contain border border-gray-200 rounded-xl bg-white p-1 shadow-sm" />
            @endif
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $negocioNom }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ now()->isoFormat('dddd D [de] MMMM, YYYY') }}
                </p>
            </div>
        </div>

        {{-- Toggle Resumen / Flujo de efectivo --}}
        <div class="flex bg-gray-100 dark:bg-gray-700 rounded-xl p-1 gap-1">
            <button wire:click="$set('vista','resumen')"
                    class="px-4 py-2 text-sm font-semibold rounded-lg transition-all
                           {{ $vista === 'resumen'
                              ? 'bg-white dark:bg-gray-800 text-indigo-700 dark:text-indigo-400 shadow-sm'
                              : 'text-gray-500 dark:text-gray-400 hover:text-gray-700' }}">
                📊 Resumen
            </button>
            <button wire:click="$set('vista','flujo')"
                    class="px-4 py-2 text-sm font-semibold rounded-lg transition-all
                           {{ $vista === 'flujo'
                              ? 'bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-400 shadow-sm'
                              : 'text-gray-500 dark:text-gray-400 hover:text-gray-700' }}">
                💵 Flujo de efectivo
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- VISTA: RESUMEN                                      --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @if($vista === 'resumen')

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Ventas hoy</p>
            <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">${{ number_format($this->ventasHoy, 2) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $this->pedidosPagadosHoy }} pedido(s) cobrado(s)</p>
        </div>
        <div class="card">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Ventas esta semana</p>
            <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">${{ number_format($this->ventasSemana, 2) }}</p>
        </div>
        <div class="card">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Ventas este mes</p>
            <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">${{ number_format($this->ventasMes, 2) }}</p>
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

    {{-- Gráfica + últimos pedidos --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card">
            <h3 class="font-medium text-gray-900 dark:text-white mb-4">Ventas últimos 7 días</h3>
            <div style="height:200px; position:relative;">
                <canvas id="graficaVentas"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-medium text-gray-900 dark:text-white">Últimos pedidos</h3>
                <a href="{{ route('pedidos.index') }}" class="text-xs text-indigo-600 hover:underline">Ver todos</a>
            </div>
            <div class="space-y-2">
                @forelse($this->ultimosPedidos as $pedido)
                    @php $badge = $pedido->estadoBadge(); @endphp
                    <a href="{{ route('pedidos.show', $pedido) }}"
                       class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div>
                            <p class="text-xs font-mono font-medium text-gray-900 dark:text-gray-100">{{ $pedido->folio }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($pedido->cliente->nombre, 18) }}</p>
                        </div>
                        <div class="text-right">
                            <span class="badge-{{ $pedido->estado }} text-xs">{{ $badge['texto'] }}</span>
                            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mt-0.5">${{ number_format($pedido->total, 2) }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">Sin pedidos aún</p>
                @endforelse
            </div>
        </div>
    </div>

    @endif {{-- fin vista resumen --}}

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- VISTA: FLUJO DE EFECTIVO                            --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @if($vista === 'flujo')

    @php $flujoHoy = $this->flujohoy; @endphp

    {{-- KPIs de efectivo hoy --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="card border-l-4 border-l-emerald-500 sm:col-span-1 col-span-2">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Entró hoy</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                ${{ number_format($flujoHoy['total'], 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Total cobrado hoy</p>
        </div>
        <div class="card">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">💵 Efectivo</p>
            <p class="text-xl font-bold text-gray-800 dark:text-gray-100">
                ${{ number_format($flujoHoy['efectivo'], 2) }}
            </p>
        </div>
        <div class="card">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">💳 Tarjeta</p>
            <p class="text-xl font-bold text-gray-800 dark:text-gray-100">
                ${{ number_format($flujoHoy['tarjeta'], 2) }}
            </p>
        </div>
        <div class="card">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">🏦 Transferencia</p>
            <p class="text-xl font-bold text-gray-800 dark:text-gray-100">
                ${{ number_format($flujoHoy['transferencia'], 2) }}
            </p>
        </div>
    </div>

    {{-- Gráfica flujo + tabla diaria --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Gráfica barras apiladas 14 días --}}
        <div class="lg:col-span-2 card">
            <h3 class="font-medium text-gray-900 dark:text-white mb-4">Dinero recibido — últimos 14 días</h3>
            <div style="height:220px; position:relative;">
                <canvas id="graficaFlujo"></canvas>
            </div>
        </div>

        {{-- Tabla diaria --}}
        <div class="card p-0 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-medium text-gray-900 dark:text-white text-sm">Detalle por día</h3>
            </div>
            <div class="overflow-y-auto" style="max-height:260px;">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0">
                        <tr>
                            <th class="text-left px-3 py-2 text-gray-500 dark:text-gray-400 font-medium">Día</th>
                            <th class="text-right px-3 py-2 text-gray-500 dark:text-gray-400 font-medium">Ops</th>
                            <th class="text-right px-3 py-2 text-gray-500 dark:text-gray-400 font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach(array_reverse($this->flujoDiario) as $dia)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30
                                   {{ $dia['total'] > 0 ? '' : 'opacity-40' }}">
                            <td class="px-3 py-2 text-gray-700 dark:text-gray-300 capitalize">{{ $dia['label'] }}</td>
                            <td class="px-3 py-2 text-right text-gray-500 dark:text-gray-400">
                                {{ $dia['operaciones'] ?: '—' }}
                            </td>
                            <td class="px-3 py-2 text-right font-semibold
                                       {{ $dia['total'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400' }}">
                                {{ $dia['total'] > 0 ? '$'.number_format($dia['total'],2) : '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @endif {{-- fin vista flujo --}}

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', renderCharts);
document.addEventListener('livewire:navigated', renderCharts);
document.addEventListener('livewire:updated', renderCharts);

function renderCharts() {
    // Gráfica resumen
    const ctxV = document.getElementById('graficaVentas');
    if (ctxV && !ctxV._chartInstance) {
        const grafica = @json($this->graficaVentas);
        ctxV._chartInstance = new Chart(ctxV, {
            type: 'bar',
            data: {
                labels: grafica.labels,
                datasets: [{ label: 'Ventas', data: grafica.values,
                    backgroundColor: 'rgba(99,102,241,0.7)', borderColor: 'rgb(99,102,241)',
                    borderWidth: 1, borderRadius: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false },
                    tooltip: { callbacks: { label: (c) => ' $'+c.parsed.y.toFixed(2) } } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => '$'+v }, grid: { color:'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Gráfica flujo de efectivo
    const ctxF = document.getElementById('graficaFlujo');
    if (ctxF && !ctxF._chartInstance) {
        const dias = @json($this->flujoDiario);
        const labels    = dias.map(d => d.label);
        const efectivo  = dias.map(d => d.efectivo);
        const tarjeta   = dias.map(d => d.tarjeta);
        const transfer  = dias.map(d => d.transferencia);
        const otro      = dias.map(d => d.otro);

        ctxF._chartInstance = new Chart(ctxF, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: '💵 Efectivo',      data: efectivo, backgroundColor: 'rgba(16,185,129,0.8)',  borderRadius: 3 },
                    { label: '💳 Tarjeta',        data: tarjeta,  backgroundColor: 'rgba(59,130,246,0.8)',  borderRadius: 3 },
                    { label: '🏦 Transferencia',  data: transfer, backgroundColor: 'rgba(139,92,246,0.8)', borderRadius: 3 },
                    { label: 'Otro',              data: otro,     backgroundColor: 'rgba(156,163,175,0.7)', borderRadius: 3 },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                    tooltip: { callbacks: { label: (c) => ' '+c.dataset.label+': $'+c.parsed.y.toFixed(2) } }
                },
                scales: {
                    x: { stacked: true, grid: { display: false } },
                    y: { stacked: true, beginAtZero: true, ticks: { callback: v => '$'+v }, grid: { color:'rgba(0,0,0,0.05)' } }
                }
            }
        });
    }
}
</script>
@endpush
