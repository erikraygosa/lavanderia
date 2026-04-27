<div>
    @if (session('exito'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('exito') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Servicios</h2>
            <p class="text-sm text-gray-500 mt-0.5">Catálogo de servicios de lavandería</p>
        </div>
        <a href="{{ route('servicios.crear') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo servicio
        </a>
    </div>

    <div class="card mb-4">
        <input wire:model.live.debounce.300ms="buscar" type="text" placeholder="Buscar servicio..." class="input-field" />
    </div>

    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Nombre</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Precio</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Unidad</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Estado</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($servicios as $servicio)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $servicio->nombre }}</p>
                            @if($servicio->descripcion)
                                <p class="text-xs text-gray-500">{{ Str::limit($servicio->descripcion, 60) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">${{ number_format($servicio->precio, 2) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ ucfirst($servicio->unidad) }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="toggleActivo({{ $servicio->id }})" class="cursor-pointer">
                                @if($servicio->activo)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Activo</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactivo</span>
                                @endif
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('servicios.editar', $servicio) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Editar</a>
                                <button wire:click="eliminar({{ $servicio->id }})"
                                    wire:confirm="¿Eliminar el servicio '{{ $servicio->nombre }}'?"
                                    class="text-red-600 hover:text-red-800 font-medium text-xs">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400">
                            No hay servicios registrados. <a href="{{ route('servicios.crear') }}" class="text-indigo-600">Agregar el primero</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($servicios->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">{{ $servicios->links() }}</div>
        @endif
    </div>
</div>
