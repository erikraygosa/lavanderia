<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('productos.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-semibold text-gray-900">{{ $producto?->exists ? 'Editar producto' : 'Nuevo producto' }}</h2>
    </div>

    <div class="max-w-xl">
        <div class="card">
            <form wire:submit="guardar" class="space-y-4">
                <div>
                    <label class="label-field">Nombre del producto *</label>
                    <input wire:model="nombre" type="text" class="input-field" placeholder="Ej. Suavizante de ropa" autofocus />
                    @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="label-field">Descripción</label>
                    <textarea wire:model="descripcion" rows="2" class="input-field resize-none" placeholder="Descripción opcional..."></textarea>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="label-field">Precio *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400 text-sm">$</span>
                            <input wire:model="precio" type="number" step="0.01" min="0" class="input-field pl-7" placeholder="0.00" />
                        </div>
                        @error('precio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label-field">Stock</label>
                        <input wire:model="stock" type="number" min="0" class="input-field" placeholder="0" />
                        @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label-field">Unidad</label>
                        <select wire:model="unidad" class="input-field">
                            @foreach($unidades as $u)
                                <option value="{{ $u }}">{{ ucfirst($u) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input wire:model="activo" type="checkbox" id="activo" class="rounded border-gray-300 text-indigo-600" />
                    <label for="activo" class="text-sm text-gray-700">Producto activo</label>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $producto?->exists ? 'Actualizar' : 'Guardar' }}
                    </button>
                    <a href="{{ route('productos.index') }}" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
