<div>
    @if (session('exito'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('exito') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Pedidos</h2>
            <p class="text-sm text-gray-500 mt-0.5">Gestión de órdenes de servicio</p>
        </div>
        <a href="{{ route('pedidos.crear') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo pedido
        </a>
    </div>

    {{-- Filtros --}}
    <div class="card mb-4">
        <div class="flex flex-wrap gap-3">
            <input wire:model.live.debounce.300ms="buscar" type="text" placeholder="Buscar por folio o cliente..." class="input-field flex-1 min-w-48" />
            <select wire:model.live="estado" class="input-field w-full sm:w-44">
                <option value="">Todos los estados</option>
                <option value="pendiente">⏳ Pendiente</option>
                <option value="terminado">✅ Listo</option>
                <option value="entregado">📦 Entregado</option>
                <option value="pagado">💵 Pagado</option>
                <option value="abandonado">❌ Abandonado</option>
            </select>
            <input wire:model.live="fecha" type="date" class="input-field w-full sm:w-44" />
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card p-0 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Folio</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Cliente</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Estado</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Entrega</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Total</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($pedidos as $pedido)
                    @php $badge = $pedido->estadoBadge(); @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('pedidos.show', $pedido) }}" class="font-mono text-indigo-600 hover:text-indigo-800 font-medium">{{ $pedido->folio }}</a>
                            <p class="text-xs text-gray-400">{{ $pedido->created_at->format('d/m/Y') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $pedido->cliente->nombre }}</p>
                            @if($pedido->es_domicilio)
                                <span class="text-xs text-orange-600">🛵 Domicilio</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge-{{ $pedido->estado }}">{{ $badge['texto'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $pedido->entregaFormateada() }}
                            @if($pedido->fecha_entrega && $pedido->fecha_entrega->isPast() && $pedido->estado === 'pendiente')
                                <span class="ml-1 text-red-500 text-xs">Vencido</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-gray-900">${{ number_format($pedido->total, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($pedido->estado === 'pendiente')
                                    <button wire:click="cambiarEstado({{ $pedido->id }}, 'pagado')"
                                        class="text-green-600 hover:text-green-800 font-medium text-xs">Cobrar</button>
                                @endif
                                <a href="{{ route('pedidos.show', $pedido) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Ver</a>
                                <a href="{{ route('pedidos.editar', $pedido) }}" class="text-gray-600 hover:text-gray-800 font-medium text-xs">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                            No hay pedidos. <a href="{{ route('pedidos.crear') }}" class="text-indigo-600">Crear el primero</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($pedidos->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">{{ $pedidos->links() }}</div>
        @endif
    </div>
</div>
