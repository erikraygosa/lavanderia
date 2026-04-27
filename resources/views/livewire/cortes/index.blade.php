<div>
    @if (session('exito'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('exito') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Cortes de caja</h2>
            <p class="text-sm text-gray-500 mt-0.5">Historial de cierres de caja</p>
        </div>
        <a href="{{ route('cortes.crear') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo corte
        </a>
    </div>

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
