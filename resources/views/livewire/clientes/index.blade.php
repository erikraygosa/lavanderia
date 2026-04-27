<div>
    {{-- Mensajes flash --}}
    @if (session('exito'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
            {{ session('exito') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Clientes</h2>
            <p class="text-sm text-gray-500 mt-0.5">Gestiona el catálogo de clientes</p>
        </div>
        <a href="{{ route('clientes.crear') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo cliente
        </a>
    </div>

    {{-- Búsqueda --}}
    <div class="card mb-4">
        <input wire:model.live.debounce.300ms="buscar" type="text" placeholder="Buscar por nombre, teléfono o correo..."
            class="input-field" />
    </div>

    {{-- Tabla --}}
    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Nombre</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Teléfono</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Correo</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Estado</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($clientes as $cliente)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $cliente->nombre }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cliente->telefono ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cliente->email ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @if($cliente->activo)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Activo</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('clientes.editar', $cliente) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs">Editar</a>
                                <button wire:click="eliminar({{ $cliente->id }})"
                                    wire:confirm="¿Eliminar a {{ $cliente->nombre }}? Esta acción no se puede deshacer."
                                    class="text-red-600 hover:text-red-800 font-medium text-xs">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400">
                            @if($buscar)
                                No se encontraron clientes con "{{ $buscar }}"
                            @else
                                No hay clientes registrados. <a href="{{ route('clientes.crear') }}" class="text-indigo-600">Agregar el primero</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($clientes->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $clientes->links() }}
            </div>
        @endif
    </div>
</div>
