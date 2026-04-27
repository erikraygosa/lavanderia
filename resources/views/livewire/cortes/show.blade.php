<div>
    {{-- Acciones --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('cortes.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-gray-900 font-mono">{{ $corte->folio }}</h2>
                <p class="text-sm text-gray-500">Generado {{ $corte->cerrado_en?->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        <button onclick="window.print()" class="btn-secondary no-print">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimir
        </button>
    </div>

    {{-- Resumen --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="card text-center">
            <p class="text-xs text-gray-400 uppercase">Período</p>
            <p class="font-medium text-gray-900 mt-1">{{ $corte->fecha_inicio->format('d/m/Y') }}</p>
            <p class="text-xs text-gray-400">al</p>
            <p class="font-medium text-gray-900">{{ $corte->fecha_fin->format('d/m/Y') }}</p>
        </div>
        <div class="card text-center">
            <p class="text-xs text-gray-400 uppercase">Pedidos</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $corte->total_pedidos }}</p>
        </div>
        <div class="card text-center col-span-2">
            <p class="text-xs text-gray-400 uppercase">Total cobrado</p>
            <p class="text-3xl font-bold text-indigo-700 mt-1">${{ number_format($corte->total_ventas, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        {{-- Desglose por método --}}
        <div class="card">
            <h3 class="font-medium text-gray-900 mb-3">Desglose por método de pago</h3>
            <div class="space-y-3">
                @foreach(['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia', 'otro' => 'Otro'] as $key => $label)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $key === 'efectivo' ? 'bg-green-400' : ($key === 'tarjeta' ? 'bg-blue-400' : ($key === 'transferencia' ? 'bg-purple-400' : 'bg-gray-400')) }}"></div>
                        <span class="text-sm text-gray-600">{{ $label }}</span>
                    </div>
                    <span class="font-medium text-gray-900">${{ number_format($corte->$key, 2) }}</span>
                </div>
                @endforeach
                <div class="flex justify-between items-center pt-2 border-t-2 border-gray-200">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="font-bold text-indigo-700">${{ number_format($corte->total_ventas, 2) }}</span>
                </div>
            </div>

            @if($corte->observaciones)
            <div class="mt-4 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-400 mb-1">Observaciones</p>
                <p class="text-sm text-gray-700">{{ $corte->observaciones }}</p>
            </div>
            @endif
        </div>

        {{-- Listado de pedidos --}}
        <div class="col-span-2 card p-0 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="font-medium text-gray-900">Pedidos incluidos ({{ $pedidos->count() }})</h3>
            </div>
            <div class="overflow-y-auto" style="max-height: 400px;">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="text-left px-4 py-2 font-medium text-gray-600">Folio</th>
                            <th class="text-left px-4 py-2 font-medium text-gray-600">Cliente</th>
                            <th class="text-left px-4 py-2 font-medium text-gray-600">Método</th>
                            <th class="text-right px-4 py-2 font-medium text-gray-600">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pedidos as $pedido)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-mono text-indigo-600 text-xs">{{ $pedido->folio }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $pedido->cliente->nombre }}</td>
                            <td class="px-4 py-2 text-gray-500 text-xs">{{ ucfirst($pedido->metodo_pago) }}</td>
                            <td class="px-4 py-2 text-right font-medium text-gray-900">${{ number_format($pedido->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">Sin pedidos en este período</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
