<div x-data>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pedidos.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-semibold text-gray-900">{{ $pedido?->exists ? 'Editar pedido '.$pedido->folio : 'Nuevo pedido' }}</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna principal --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Selección de cliente --}}
            <div class="card">
                <h3 class="font-medium text-gray-900 mb-3">Cliente</h3>

                @if($clienteId)
                    <div class="flex items-center justify-between bg-indigo-50 rounded-lg px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                {{ strtoupper(substr($clienteNombre, 0, 1)) }}
                            </div>
                            <span class="font-medium text-gray-900">{{ $clienteNombre }}</span>
                        </div>
                        <button wire:click="limpiarCliente" class="text-gray-400 hover:text-gray-600 text-sm">Cambiar</button>
                    </div>
                @else
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="clienteBuscar"
                            wire:keyup="buscarCliente"
                            type="text"
                            class="input-field"
                            placeholder="Buscar cliente por nombre o teléfono..." />

                        @if(count($clientesSugeridos) > 0)
                            <div class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                                @foreach($clientesSugeridos as $c)
                                    <button wire:click="seleccionarCliente({{ $c['id'] }}, '{{ addslashes($c['nombre']) }}')"
                                        class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 flex items-center justify-between text-sm">
                                        <span class="font-medium">{{ $c['nombre'] }}</span>
                                        <span class="text-gray-400">{{ $c['telefono'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @error('clienteId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @endif
            </div>

            {{-- Items --}}
            <div class="card">
                <h3 class="font-medium text-gray-900 mb-3">Servicios y productos</h3>

                {{-- Buscador de items --}}
                <div class="relative mb-4">
                    <input wire:model.live.debounce.300ms="itemBuscar"
                        wire:keyup="buscarItem"
                        type="text"
                        class="input-field"
                        placeholder="Buscar servicio o producto para agregar..." />

                    @if(count($itemsSugeridos) > 0)
                        <div class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                            @foreach($itemsSugeridos as $item)
                                <button wire:click="agregarItem({{ $item['id'] }}, '{{ $item['tipo'] }}', '{{ addslashes($item['nombre']) }}', {{ $item['precio'] }})"
                                    class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs px-1.5 py-0.5 rounded {{ $item['tipo'] === 'servicio' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ ucfirst($item['tipo']) }}
                                        </span>
                                        <span class="font-medium">{{ $item['nombre'] }}</span>
                                    </div>
                                    <span class="text-gray-700 font-medium">${{ number_format($item['precio'], 2) }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                @error('items') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror

                {{-- Lista de items --}}
                @if(count($items) > 0)
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-3 py-2 font-medium text-gray-600">Descripción</th>
                                    <th class="text-center px-3 py-2 font-medium text-gray-600 w-24">Cantidad</th>
                                    <th class="text-right px-3 py-2 font-medium text-gray-600 w-28">Precio</th>
                                    <th class="text-right px-3 py-2 font-medium text-gray-600 w-24">Subtotal</th>
                                    <th class="w-8"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($items as $idx => $item)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <p class="font-medium text-gray-900">{{ $item['descripcion'] }}</p>
                                            <span class="text-xs {{ $item['tipo'] === 'servicio' ? 'text-blue-600' : 'text-amber-600' }}">
                                                {{ ucfirst($item['tipo']) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" step="0.001" min="0.001"
                                                wire:change="actualizarCantidad({{ $idx }}, $event.target.value)"
                                                value="{{ $item['cantidad'] }}"
                                                class="w-full text-center border border-gray-200 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-400" />
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="relative">
                                                <span class="absolute left-2 top-1 text-gray-400 text-xs">$</span>
                                                <input type="number" step="0.01" min="0"
                                                    wire:change="actualizarPrecio({{ $idx }}, $event.target.value)"
                                                    value="{{ $item['precio_unitario'] }}"
                                                    class="w-full text-right border border-gray-200 rounded px-2 py-1 pl-5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-400" />
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 text-right font-medium text-gray-900">
                                            ${{ number_format($item['subtotal'], 2) }}
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <button wire:click="quitarItem({{ $idx }})" class="text-red-400 hover:text-red-600">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 text-gray-400 text-sm border-2 border-dashed border-gray-200 rounded-lg">
                        Busca y agrega servicios o productos al pedido
                    </div>
                @endif
            </div>

            {{-- Notas --}}
            <div class="card">
                <label class="label-field">Notas del pedido</label>
                <textarea wire:model="notas" rows="2" class="input-field resize-none" placeholder="Instrucciones especiales, observaciones..."></textarea>
            </div>
        </div>

        {{-- Columna lateral --}}
        <div class="space-y-4">

            {{-- Fecha y hora de entrega --}}
            <div class="card">
                <h3 class="font-medium text-gray-900 mb-3">Fecha y hora de entrega</h3>
                <div class="space-y-2">
                    <div>
                        <label class="label-field">Día</label>
                        <input wire:model="fechaEntrega" type="date" class="input-field" />
                        @error('fechaEntrega') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label-field">Hora estimada</label>
                        <input wire:model="horaEntrega" type="time" class="input-field" />
                        <p class="text-xs text-gray-400 mt-1">Por defecto: 18:00 hrs</p>
                    </div>
                </div>
            </div>

            {{-- Resumen --}}
            <div class="card">
                <h3 class="font-medium text-gray-900 mb-3">Resumen</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">${{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Descuento</span>
                        <div class="relative w-28">
                            <span class="absolute left-2 top-1 text-gray-400 text-xs">$</span>
                            <input wire:model.lazy="descuento" type="number" step="0.01" min="0"
                                class="w-full text-right border border-gray-200 rounded px-2 py-1 pl-5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-400" />
                        </div>
                    </div>
                    @if((float)$descuento > 0)
                    <div>
                        <input wire:model.lazy="descuentoNota" type="text"
                            placeholder="Motivo del descuento (opcional)"
                            class="w-full border border-gray-200 rounded px-2 py-1 text-xs text-gray-600 focus:outline-none focus:ring-1 focus:ring-indigo-400" />
                    </div>
                    @endif
                    <div class="flex justify-between pt-2 border-t border-gray-200">
                        <span class="font-semibold text-gray-900">Total</span>
                        <span class="font-bold text-lg text-indigo-700">${{ number_format($this->total, 2) }}</span>
                    </div>
                </div>

                <button wire:click="guardar" class="btn-primary w-full justify-center mt-4">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $pedido?->exists ? 'Actualizar pedido' : 'Guardar pedido' }}
                </button>
                <a href="{{ route('pedidos.index') }}" class="btn-secondary w-full justify-center mt-2">Cancelar</a>
            </div>
        </div>
    </div>
</div>
