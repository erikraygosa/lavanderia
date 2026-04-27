<div>
    @if (session('exito'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('exito') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Productos</h2>
            <p class="text-sm text-gray-500 mt-0.5">Catálogo de productos e insumos</p>
        </div>
        <a href="{{ route('productos.crear') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo producto
        </a>
    </div>

    <div class="card mb-4">
        <input wire:model.live.debounce.300ms="buscar" type="text" placeholder="Buscar producto..." class="input-field" />
    </div>

    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Nombre</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Precio</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Stock</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Unidad</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Estado</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($productos as $producto)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $producto->nombre }}</p>
                            @if($producto->descripcion)
                                <p class="text-xs text-gray-500">{{ Str::limit($producto->descripcion, 50) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">${{ number_format($producto->precio, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $producto->stock <= 5 ? 'text-red-600 font-medium' : 'text-gray-700' }}">
                                {{ $producto->stock }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ ucfirst($producto->unidad) }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="toggleActivo({{ $producto->id }})">
                                @if($producto->activo)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Activo</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactivo</span>
                                @endif
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('productos.editar', $producto) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Editar</a>
                                <button wire:click="eliminar({{ $producto->id }})"
                                    wire:confirm="¿Eliminar el producto '{{ $producto->nombre }}'?"
                                    class="text-red-600 hover:text-red-800 font-medium text-xs">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                            No hay productos registrados. <a href="{{ route('productos.crear') }}" class="text-indigo-600">Agregar el primero</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($productos->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">{{ $productos->links() }}</div>
        @endif
    </div>
</div>
