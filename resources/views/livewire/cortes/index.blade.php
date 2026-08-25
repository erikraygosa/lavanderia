<div>
    @if (session('exito'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('exito') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Cortes de caja</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Historial de cierres de caja</p>
        </div>
        <a href="{{ route('cortes.crear') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo corte
        </a>
    </div>

    {{-- ── Gráfica ventas por mes ──────────────────────────────────────────── --}}
    @php $vm = $this->ventasMensuales; @endphp
    <div class="card mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white">Dinero recibido por mes</h3>
            <span class="text-xs text-gray-400 dark:text-gray-500">Últimos 12 meses</span>
        </div>

        {{-- KPIs rápidos --}}
        @php
            $totalAnio   = array_sum($vm['totales']);
            $mejorIdx    = array_search(max($vm['totales']), $vm['totales']);
            $mejorLabel  = $vm['labels'][$mejorIdx] ?? '—';
            $mejorMonto  = $vm['totales'][$mejorIdx] ?? 0;
            $mesActualIdx = 11; // último del array = mes en curso
        @endphp
        <div class="grid grid-cols-3 gap-4 mb-5">
            <div class="bg-indigo-50 dark:bg-indigo-900/30 rounded-xl p-3 text-center">
                <p class="text-xs text-indigo-500 dark:text-indigo-400 uppercase tracking-wide mb-1">Total 12 meses</p>
                <p class="text-xl font-bold text-indigo-700 dark:text-indigo-300">${{ number_format($totalAnio, 2) }}</p>
            </div>
            <div class="bg-emerald-50 dark:bg-emerald-900/30 rounded-xl p-3 text-center">
                <p class="text-xs text-emerald-600 dark:text-emerald-400 uppercase tracking-wide mb-1">Mejor mes</p>
                <p class="text-xl font-bold text-emerald-700 dark:text-emerald-300">${{ number_format($mejorMonto, 2) }}</p>
                <p class="text-xs text-emerald-500 dark:text-emerald-400 capitalize">{{ $mejorLabel }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Mes actual</p>
                <p class="text-xl font-bold text-gray-700 dark:text-gray-200">${{ number_format($vm['totales'][$mesActualIdx], 2) }}</p>
                <p class="text-xs text-gray-400 capitalize">{{ $vm['pedidos'][$mesActualIdx] }} pedido(s)</p>
            </div>
        </div>

        {{-- Canvas + botón de mes seleccionado --}}
        <div x-data="{
                datos: {{ Js::from($vm) }},
                fechas: {{ Js::from($vm['fechas'] ?? []) }},
                mesSeleccionado: null,
                init() {
                    const ctx = this.$el.querySelector('canvas');
                    const ex  = Chart.getChart(ctx);
                    if (ex) ex.destroy();
                    const self = this;
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.datos.labels,
                            datasets: [{
                                label: 'Dinero recibido',
                                data: this.datos.totales,
                                backgroundColor: 'rgba(99,102,241,0.75)',
                                borderColor: 'rgb(99,102,241)',
                                borderWidth: 1,
                                borderRadius: 5,
                                hoverBackgroundColor: 'rgba(99,102,241,0.95)',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            onClick(e, elements) {
                                if (!elements.length) { self.mesSeleccionado = null; return; }
                                const i = elements[0].index;
                                self.mesSeleccionado = {
                                    label:  self.datos.labels[i],
                                    monto:  self.datos.totales[i],
                                    desde:  self.datos.fechas[i].desde,
                                    hasta:  self.datos.fechas[i].hasta,
                                };
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: (c) => ' $' + c.parsed.y.toLocaleString('es-MX', {minimumFractionDigits:2}),
                                        afterLabel: (c) => ' ' + self.datos.pedidos[c.dataIndex] + ' pedido(s)'
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { callback: v => '$' + v.toLocaleString('es-MX') },
                                    grid: { color: 'rgba(0,0,0,0.04)' }
                                },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
             }">
            <div style="height:240px; position:relative;">
                <canvas></canvas>
            </div>

            {{-- Botón flotante al seleccionar un mes --}}
            <div x-show="mesSeleccionado" x-transition
                 class="mt-4 flex items-center gap-3 p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl border border-indigo-200 dark:border-indigo-700">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-indigo-800 dark:text-indigo-200 capitalize"
                       x-text="mesSeleccionado ? mesSeleccionado.label : ''"></p>
                    <p class="text-xs text-indigo-500 dark:text-indigo-400"
                       x-text="mesSeleccionado ? '$' + (mesSeleccionado.monto).toLocaleString('es-MX', {minimumFractionDigits:2}) + ' recibidos' : ''"></p>
                </div>
                <a x-bind:href="mesSeleccionado ? '{{ route('pedidos.index') }}?desde=' + mesSeleccionado.desde + '&hasta=' + mesSeleccionado.hasta : '#'"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Ver pedidos de <span class="capitalize" x-text="mesSeleccionado ? mesSeleccionado.label : ''"></span>
                </a>
                <button @click="mesSeleccionado = null" class="text-indigo-400 hover:text-indigo-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Tabla de cortes ─────────────────────────────────────────────────── --}}
    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Folio</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Período</th>
                    <th class="text-center px-4 py-3 font-medium text-gray-600">Pedidos</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Total ventas</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Cerrado</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($cortes as $corte)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono font-medium text-indigo-600">{{ $corte->folio }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $corte->fecha_inicio->format('d/m/Y') }} — {{ $corte->fecha_fin->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $corte->total_pedidos }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">${{ number_format($corte->total_ventas, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-500 text-xs">
                            {{ $corte->cerrado_en?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('cortes.show', $corte) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Ver detalle</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                            No hay cortes registrados. <a href="{{ route('cortes.crear') }}" class="text-indigo-600">Generar el primero</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($cortes->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">{{ $cortes->links() }}</div>
        @endif
    </div>
</div>
