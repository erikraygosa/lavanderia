<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('clientes.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ $cliente?->exists ? 'Editar cliente' : 'Nuevo cliente' }}</h2>
        </div>
    </div>

    <div class="max-w-2xl">
        <div class="card">
            <form wire:submit="guardar" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="label-field">Nombre completo *</label>
                        <input wire:model="nombre" type="text" class="input-field" placeholder="Ej. Juan Pérez García" autofocus />
                        @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label-field">Teléfono</label>
                        <input wire:model="telefono" type="text" class="input-field" placeholder="Ej. 555 123 4567" />
                        @error('telefono') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label-field">Correo electrónico</label>
                        <input wire:model="email" type="email" class="input-field" placeholder="correo@ejemplo.com" />
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="label-field">Dirección</label>
                        <input wire:model="direccion" type="text" class="input-field" placeholder="Calle, colonia, ciudad..." />
                    </div>

                    <div class="col-span-2">
                        <label class="label-field">Notas</label>
                        <textarea wire:model="notas" rows="3" class="input-field resize-none" placeholder="Observaciones del cliente..."></textarea>
                    </div>

                    <div class="col-span-2 flex items-center gap-3">
                        <input wire:model="activo" type="checkbox" id="activo" class="rounded border-gray-300 text-indigo-600" />
                        <label for="activo" class="text-sm text-gray-700">Cliente activo</label>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $cliente?->exists ? 'Actualizar' : 'Guardar' }}
                    </button>
                    <a href="{{ route('clientes.index') }}" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
