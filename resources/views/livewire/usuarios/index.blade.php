<div>
    @if (session('exito'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('exito') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Usuarios</h2>
            <p class="text-sm text-gray-500 mt-0.5">Gestión de acceso al sistema</p>
        </div>
        <a href="{{ route('usuarios.crear') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo usuario
        </a>
    </div>

    <div class="card mb-4">
        <input wire:model.live.debounce.300ms="buscar" type="text"
               placeholder="Buscar por nombre o correo..."
               class="input-field max-w-sm" />
    </div>

    <div class="card p-0 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Usuario</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden sm:table-cell">Rol</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 hidden lg:table-cell">Permisos</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($usuarios as $usuario)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0
                                            {{ $usuario->esAdmin() ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                    {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 flex items-center gap-1.5">
                                        {{ $usuario->name }}
                                        @if($usuario->id === auth()->id())
                                            <span class="text-xs text-indigo-500 font-normal">(tú)</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $usuario->email }}</p>
                                    {{-- Rol visible en móvil --}}
                                    <span class="sm:hidden inline-block mt-1 text-xs px-2 py-0.5 rounded-full font-medium
                                                 {{ $usuario->esAdmin() ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $usuario->esAdmin() ? 'Administrador' : 'Operador' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium
                                         {{ $usuario->esAdmin() ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                                @if($usuario->esAdmin())
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                    Administrador
                                @else
                                    Operador
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @if($usuario->esAdmin())
                                <span class="text-xs text-gray-400 italic">Todos los permisos</span>
                            @elseif(!empty($usuario->permisos))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($usuario->permisos as $p)
                                        <span class="text-xs bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded font-mono">{{ $p }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-amber-600">Sin permisos asignados</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('usuarios.editar', $usuario) }}"
                                   class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Editar</a>
                                @if($usuario->id !== auth()->id())
                                    <button wire:click="eliminar({{ $usuario->id }})"
                                            wire:confirm="¿Eliminar al usuario {{ $usuario->name }}?"
                                            class="text-red-500 hover:text-red-700 text-xs font-medium">Eliminar</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-gray-400">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($usuarios->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">{{ $usuarios->links() }}</div>
        @endif
    </div>
</div>
