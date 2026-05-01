<div>
    @if (session('exito'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg text-green-700 dark:text-green-300 text-sm">{{ session('exito') }}</div>
    @endif

    @if($mensajeWhatsapp)
        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg text-blue-700 dark:text-blue-300 text-sm">{{ $mensajeWhatsapp }}</div>
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
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Cliente</p>
                        <p class="font-semibold text-gray-900 dark:text-white text-lg">{{ $pedido->cliente->nombre }}</p>
                        @if($pedido->cliente->telefono)
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $pedido->cliente->telefono }}</p>
                        @endif
                        @if($pedido->cliente->email)
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $pedido->cliente->email }}</p>
                        @endif
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Entrega</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ $pedido->entregaFormateada() }}</p>
                        @if($pedido->es_domicilio)
                            <span class="inline-flex items-center gap-1 text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full mt-1">
                                🛵 Domicilio
                            </span>
                        @endif
                        @if($pedido->fecha_entrega && $pedido->fecha_entrega->isPast() && $pedido->estado === 'pendiente')
                            <span class="block text-xs text-red-600 font-medium mt-1">⚠️ Vencido</span>
                        @endif
                    </div>
                </div>
                @if($pedido->es_domicilio && $pedido->direccion_domicilio)
                    <div class="mt-2 pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-400 mb-0.5">📍 Dirección de entrega</p>
                        <p class="text-sm text-gray-700">{{ $pedido->direccion_domicilio }}</p>
                    </div>
                @endif
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
                    <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Concepto</th>
                            <th class="text-center px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Cant.</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Precio</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($pedido->items as $item)
                            <tr class="dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $item->descripcion }}</p>
                                    <span class="text-xs {{ $item->tipo === 'servicio' ? 'text-blue-600 dark:text-blue-400' : 'text-amber-600 dark:text-amber-400' }}">{{ ucfirst($item->tipo) }}</span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $item->cantidad + 0 }}</td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">${{ number_format($item->precio_unitario, 2) }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-gray-100">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right text-sm text-gray-600 dark:text-gray-400">Subtotal</td>
                            <td class="px-4 py-2 text-right font-medium dark:text-gray-200">${{ number_format($pedido->subtotal, 2) }}</td>
                        </tr>
                        @if($pedido->descuento > 0)
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right text-sm text-gray-600 dark:text-gray-400">
                                Descuento
                                @if($pedido->descuento_nota)
                                    <span class="block text-xs text-gray-400 italic">{{ $pedido->descuento_nota }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right text-red-600 dark:text-red-400 font-medium">-${{ number_format($pedido->descuento, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td colspan="3" class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">Total</td>
                            <td class="px-4 py-3 text-right font-bold text-lg text-indigo-700 dark:text-indigo-400">${{ number_format($pedido->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Panel lateral --}}
        <div class="space-y-4">

            {{-- Estado y acciones --}}
            <div class="card">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Estado</p>
                @php $badge = $pedido->estadoBadge(); @endphp
                <span class="badge-{{ $pedido->estado }} text-sm px-3 py-1">
                    {{ $badge['icon'] }} {{ $badge['texto'] }}
                </span>

                {{-- PENDIENTE --}}
                @if($pedido->estado === 'pendiente')
                    <div class="mt-4 space-y-2">
                        <button wire:click="marcarTerminado" class="btn-accion-azul">
                            ✅ Marcar como listo
                        </button>
                        <button wire:click="marcarAbandonado"
                                wire:confirm="¿Marcar como abandonado?"
                                class="btn-accion-rojo opacity-80">
                            ❌ Marcar abandonado
                        </button>
                    </div>
                @endif

                {{-- TERMINADO (listo) --}}
                @if($pedido->estado === 'terminado')
                    <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                        Listo el: <strong class="dark:text-gray-300">{{ $pedido->terminado_en?->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div class="mt-4 space-y-2">
                        {{-- Notificar al cliente: botón o formulario inline --}}
                        @if(!$mostrarFormNotificar)
                            <button wire:click="abrirFormNotificar" class="btn-accion-verde">
                                💬 Notificar al cliente (WhatsApp)
                            </button>
                        @else
                            {{-- Formulario editable de fecha/hora --}}
                            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-xl p-3 space-y-2">
                                <p class="text-xs font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-1">
                                    💬 Confirmar hora de recogida
                                </p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-xs text-gray-500 dark:text-gray-400 mb-0.5 block">Fecha</label>
                                        <input wire:model="notificarFecha" type="date" class="input-field text-xs py-1.5" />
                                        @error('notificarFecha') <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 dark:text-gray-400 mb-0.5 block">Hora</label>
                                        <input wire:model="notificarHora" type="time" class="input-field text-xs py-1.5" />
                                        @error('notificarHora') <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="flex gap-2 pt-1">
                                    <button wire:click="notificarListo" wire:loading.attr="disabled"
                                            class="flex-1 flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold py-2 rounded-lg transition-colors">
                                        <svg wire:loading wire:target="notificarListo" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                        <svg wire:loading.remove wire:target="notificarListo" class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        Enviar WhatsApp
                                    </button>
                                    <button wire:click="$set('mostrarFormNotificar', false)"
                                            class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg transition-colors">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Marcar entregado --}}
                        <button wire:click="marcarEntregado" class="btn-accion-morado">
                            📦 Marcar como entregado
                        </button>

                        {{-- Cobrar --}}
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700 space-y-2">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Cobrar ahora</p>
                            <select wire:model="metodoPago" class="input-field">
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="otro">Otro</option>
                            </select>
                            <button wire:click="marcarPagado" class="btn-accion-indigo">
                                💵 Cobrar pedido
                            </button>
                        </div>

                        <button wire:click="marcarPendiente"
                                class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 w-full text-center mt-1">
                            Revertir a pendiente
                        </button>
                    </div>
                @endif

                {{-- ENTREGADO --}}
                @if($pedido->estado === 'entregado')
                    <div class="mt-3 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                        @if($pedido->terminado_en)
                            <p>Listo el: <strong class="dark:text-gray-300">{{ $pedido->terminado_en->format('d/m/Y H:i') }}</strong></p>
                        @endif
                        <p>Entregado el: <strong class="dark:text-gray-300">{{ $pedido->entregado_en?->format('d/m/Y H:i') }}</strong></p>
                        @if($pedido->pagado_en)
                            <p>Pagado el: <strong class="dark:text-gray-300">{{ $pedido->pagado_en->format('d/m/Y H:i') }}</strong></p>
                            @if($pedido->metodo_pago)
                                <p>Método: <strong class="dark:text-gray-300">{{ ucfirst($pedido->metodo_pago) }}</strong></p>
                            @endif
                        @endif
                    </div>
                    @if(!$pedido->pagado_en)
                    <div class="mt-4 space-y-2">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Registrar pago</p>
                        <select wire:model="metodoPago" class="input-field">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="otro">Otro</option>
                        </select>
                        <button wire:click="marcarPagado" class="btn-accion-indigo">
                            💵 Cobrar pedido
                        </button>
                    </div>
                    @endif
                    <button wire:click="marcarPendiente"
                            class="mt-3 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 w-full text-center block">
                        Revertir a pendiente
                    </button>
                @endif

                {{-- PAGADO --}}
                @if($pedido->estado === 'pagado')
                    <div class="mt-3 text-sm space-y-1">
                        @if($pedido->terminado_en)
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Listo el: <strong class="dark:text-gray-300">{{ $pedido->terminado_en->format('d/m/Y H:i') }}</strong>
                            </p>
                        @endif
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Pagado el: <strong class="dark:text-gray-300">{{ $pedido->pagado_en?->format('d/m/Y H:i') }}</strong>
                        </p>
                        @if($pedido->metodo_pago)
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Método: <strong class="dark:text-gray-300">{{ ucfirst($pedido->metodo_pago) }}</strong>
                            </p>
                        @endif
                    </div>
                    <div class="mt-4 space-y-2">
                        <button wire:click="marcarEntregado" class="btn-accion-morado">
                            📦 Marcar como entregado
                        </button>
                        <button wire:click="marcarPendiente"
                                class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 w-full text-center">
                            Revertir a pendiente
                        </button>
                    </div>
                @endif

                {{-- ABANDONADO --}}
                @if($pedido->estado === 'abandonado')
                    <div class="mt-3">
                        <button wire:click="marcarPendiente" class="btn-accion-azul">
                            ♻️ Reactivar pedido
                        </button>
                    </div>
                @endif
            </div>

            {{-- Total --}}
            <div class="card text-center">
                <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">${{ number_format($pedido->total, 2) }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total del pedido</p>
                @if($pedido->es_domicilio)
                    <span class="inline-block mt-2 text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">
                        🛵 Envío a domicilio
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
