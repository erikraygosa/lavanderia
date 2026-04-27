<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('usuarios.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-xl font-semibold text-gray-900">
            {{ $usuario?->exists ? 'Editar usuario' : 'Nuevo usuario' }}
        </h2>
    </div>

    <div class="max-w-xl space-y-4">

        {{-- Datos básicos --}}
        <div class="card space-y-4">
            <h3 class="font-medium text-gray-900 border-b border-gray-100 pb-2">Datos de acceso</h3>

            <div>
                <label class="label-field">Nombre completo</label>
                <input wire:model="name" type="text" class="input-field" placeholder="Ej. María García" />
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label-field">Correo electrónico</label>
                <input wire:model="email" type="email" class="input-field" placeholder="correo@ejemplo.com" />
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label-field">
                        Contraseña
                        @if($usuario?->exists)
                            <span class="text-gray-400 font-normal ml-1 text-xs">(vacío = sin cambios)</span>
                        @endif
                    </label>
                    <input wire:model="password" type="password" class="input-field"
                           placeholder="{{ $usuario?->exists ? '••••••••' : 'Mínimo 6 caracteres' }}" />
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label-field">Confirmar contraseña</label>
                    <input wire:model="password_confirmation" type="password" class="input-field"
                           placeholder="Repite la contraseña" />
                </div>
            </div>
        </div>

        {{-- Rol --}}
        <div class="card space-y-3">
            <h3 class="font-medium text-gray-900 border-b border-gray-100 pb-2">Rol</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label class="flex items-start gap-3 p-3 rounded-lg border-2 cursor-pointer transition-colors
                              {{ $rol === 'admin' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">
                    <input wire:model.live="rol" type="radio" value="admin" class="mt-0.5 text-indigo-600 focus:ring-indigo-400" />
                    <div>
                        <p class="font-medium text-gray-900 text-sm">Administrador</p>
                        <p class="text-xs text-gray-500 mt-0.5">Acceso completo a todas las secciones</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3 rounded-lg border-2 cursor-pointer transition-colors
                              {{ $rol === 'operador' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">
                    <input wire:model.live="rol" type="radio" value="operador" class="mt-0.5 text-indigo-600 focus:ring-indigo-400" />
                    <div>
                        <p class="font-medium text-gray-900 text-sm">Operador</p>
                        <p class="text-xs text-gray-500 mt-0.5">Acceso limitado según permisos</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Permisos (solo si es operador) --}}
        @if($rol === 'operador')
        <div class="card">
            <h3 class="font-medium text-gray-900 border-b border-gray-100 pb-2 mb-3">Permisos</h3>
            <p class="text-xs text-gray-500 mb-3">Selecciona a qué secciones puede acceder este operador.</p>

            <div class="space-y-2">
                @foreach($permisosDisponibles as $clave => $etiqueta)
                    <label class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 cursor-pointer group">
                        <input wire:model="permisos" type="checkbox" value="{{ $clave }}"
                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-400" />
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-800">{{ $etiqueta }}</span>
                            <span class="ml-2 text-xs text-gray-400 font-mono">{{ $clave }}</span>
                        </div>
                        @if(in_array($clave, $permisos))
                            <span class="text-xs text-indigo-600 font-medium">✓ Activo</span>
                        @endif
                    </label>
                @endforeach
            </div>

            @if(empty($permisos))
                <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded px-3 py-2 mt-3">
                    ⚠️ Sin permisos seleccionados: el operador solo podrá ver pedidos y clientes.
                </p>
            @endif
        </div>
        @else
        <div class="card bg-indigo-50 border border-indigo-200">
            <div class="flex items-center gap-2 text-indigo-700 text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                </svg>
                Los administradores tienen acceso a todas las secciones automáticamente.
            </div>
        </div>
        @endif

        {{-- Botones --}}
        <div class="flex flex-wrap gap-3">
            <button wire:click="guardar" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $usuario?->exists ? 'Actualizar usuario' : 'Crear usuario' }}
            </button>
            <a href="{{ route('usuarios.index') }}" class="btn-secondary">Cancelar</a>
        </div>

    </div>
</div>
