<div>
    @if (session('exito'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('exito') }}</div>
    @endif

    @if($mensajeWhatsapp)
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm">{{ $mensajeWhatsapp }}</div>
    @endif

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('pedidos.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-gray-900 font-mono">{{ $pedido->folio }}</h2>
                <p class="text-sm text-gray-500">{{ $pedido->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pedidos.ticket', $pedido) }}" target="_blank" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimir ticket
            </a>
            <button wire:click="enviarWhatsapp" wire:loading.attr="disabled" class="btn-secondary">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                WhatsApp
            </button>
            <a href="{{ route('pedidos.editar', $pedido) }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Detalle principal --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Info cliente --}}
            <div class="card">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Cliente</p>
                        <p class="font-semibold text-gray-900 text-lg">{{ $pedido->cliente->nombre }}</p>
                        @if($pedido->cliente->telefono)
                            <p class="text-gray-500 text-sm">{{ $pedido->cliente->telefono }}</p>
                        @endif
                        @if($pedido->cliente->email)
                            <p class="text-gray-500 text-sm">{{ $pedido->cliente->email }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 mb-1">Fecha de entrega</p>
                        <p class="font-medium text-gray-900">{{ $pedido->entregaFormateada() }}</p>
                        @if($pedido->fecha_entrega && $pedido->fecha_entrega->isPast() && $pedido->estado === 'pendiente')
                            <span class="text-xs text-red-600 font-medium">Vencido</span>
                        @endif
                    </div>
                </div>
                @if($pedido->notas)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-400 mb-1">Notas</p>
                        <p class="text-sm text-gray-700">{{ $pedido->notas }}</p>
                    </div>
                @endif
            </div>

            {{-- Items --}}
            <div class="card p-0 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Concepto</th>
                            <th class="text-center px-4 py-3 font-medium text-gray-600">Cant.</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">Precio</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pedido->items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $item->descripcion }}</p>
                                    <span class="text-xs {{ $item->tipo === 'servicio' ? 'text-blue-600' : 'text-amber-600' }}">{{ ucfirst($item->tipo) }}</span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700">{{ $item->cantidad + 0 }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">${{ number_format($item->precio_unitario, 2) }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right text-sm text-gray-600">Subtotal</td>
                            <td class="px-4 py-2 text-right font-medium">${{ number_format($pedido->subtotal, 2) }}</td>
                        </tr>
                        @if($pedido->descuento > 0)
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right text-sm text-gray-600">
                                Descuento
                                @if($pedido->descuento_nota)
                                    <span class="block text-xs text-gray-400 italic">{{ $pedido->descuento_nota }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right text-red-600 font-medium">-${{ number_format($pedido->descuento, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-t border-gray-200">
                            <td colspan="3" class="px-4 py-3 text-right font-semibold text-gray-900">Total</td>
                            <td class="px-4 py-3 text-right font-bold text-lg text-indigo-700">${{ number_format($pedido->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Panel de estado --}}
        <div class="space-y-4">
            <div class="card">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Estado</p>
                @php $badge = $pedido->estadoBadge(); @endphp
                <span class="badge-{{ $pedido->estado }} text-sm px-3 py-1">{{ $badge['texto'] }}</span>

                @if($pedido->estado === 'pendiente')
                    <div class="mt-4 space-y-2">
                        <p class="text-xs font-medium text-gray-600">Método de pago</p>
                        <select wire:model="metodoPago" class="input-field">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="otro">Otro</option>
                        </select>
                        <button wire:click="marcarPagado" class="btn-primary w-full justify-center mt-2">
                            Marcar como pagado
                        </button>
                        <button wire:click="marcarAbandonado"
                            wire:confirm="¿Marcar como abandonado este pedido?"
                            class="btn-secondary w-full justify-center text-red-600 hover:text-red-700">
                            Marcar abandonado
                        </button>
                    </div>
                @elseif($pedido->estado === 'pagado')
                    <div class="mt-3 text-sm">
                        <p class="text-gray-600">Pagado el: <strong>{{ $pedido->pagado_en?->format('d/m/Y H:i') }}</strong></p>
                        <p class="text-gray-600">Método: <strong>{{ ucfirst($pedido->metodo_pago) }}</strong></p>
                        <button wire:click="marcarPendiente" class="mt-3 text-xs text-gray-400 hover:text-gray-600">
                            Revertir a pendiente
                        </button>
                    </div>
                @elseif($pedido->estado === 'abandonado')
                    <div class="mt-3">
                        <button wire:click="marcarPendiente" class="btn-secondary w-full justify-center text-sm">
                            Reactivar pedido
                        </button>
                    </div>
                @endif
            </div>

            <div class="card text-center">
                <p class="text-2xl font-bold text-indigo-700">${{ number_format($pedido->total, 2) }}</p>
                <p class="text-sm text-gray-500 mt-1">Total del pedido</p>
            </div>
        </div>
    </div>
</div>
