<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('cortes.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-semibold text-gray-900">Nuevo corte de caja</h2>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2 space-y-4">

            {{-- Rango de fechas --}}
            <div class="card">
                <h3 class="font-medium text-gray-900 mb-4">Período del corte</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label-field">Fecha de inicio *</label>
                        <input wire:model="fechaInicio" type="date" class="input-field" />
                        @error('fechaInicio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label-field">Fecha de fin *</label>
                        <input wire:model="fechaFin" type="date" class="input-field" />
                        @error('fechaFin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-2 mt-3">
                    <button wire:click="$set('fechaInicio', '{{ now()->startOfDay()->format('Y-m-d') }}')" class="text-xs px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50">Hoy</button>
                    <button wire:click="$set('fechaInicio', '{{ now()->startOfWeek()->format('Y-m-d') }}')" class="text-xs px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50">Esta semana</button>
                    <button wire:click="$set('fechaInicio', '{{ now()->startOfMonth()->format('Y-m-d') }}')" class="text-xs px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50">Este mes</button>
                </div>

                <button wire:click="calcular" class="btn-secondary mt-4">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Calcular totales
                </button>
            </div>

            {{-- Preview --}}
            @if($preview)
            <div class="card">
                <h3 class="font-medium text-gray-900 mb-4">Vista previa del corte</h3>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-indigo-50 rounded-lg p-4 text-center">
                        <p class="text-xs text-indigo-500 uppercase">Total ventas</p>
                        <p class="text-2xl font-bold text-indigo-700">${{ number_format($preview['total_ventas'], 2) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-500 uppercase">Pedidos cobrados</p>
                        <p class="text-2xl font-bold text-gray-700">{{ $preview['total_pedidos'] }}</p>
                    </div>
                </div>

                <h4 class="text-sm font-medium text-gray-700 mb-3">Desglose por método de pago</h4>
                <div class="space-y-2">
                    @foreach(['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia', 'otro' => 'Otro'] as $key => $label)
                    @if($preview[$key] > 0)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">{{ $label }}</span>
                        <span class="font-medium text-gray-900">${{ number_format($preview[$key], 2) }}</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            <div class="card">
                <label class="label-field">Observaciones</label>
                <textarea wire:model="observaciones" rows="2" class="input-field resize-none" placeholder="Notas adicionales del corte..."></textarea>
            </div>
        </div>

        {{-- Acciones --}}
        <div>
            <div class="card">
                <h3 class="font-medium text-gray-900 mb-3">Generar corte</h3>
                <p class="text-sm text-gray-500 mb-4">El corte incluirá todos los pedidos con estado "pagado" en el período seleccionado.</p>

                @if($preview)
                    <div class="bg-indigo-50 rounded-lg p-3 mb-4">
                        <p class="text-sm font-bold text-indigo-700 text-center">${{ number_format($preview['total_ventas'], 2) }}</p>
                        <p class="text-xs text-indigo-500 text-center">{{ $preview['total_pedidos'] }} pedidos</p>
                    </div>
                @endif

                <button wire:click="guardar" class="btn-primary w-full justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Guardar corte
                </button>
                <a href="{{ route('cortes.index') }}" class="btn-secondary w-full justify-center mt-2">Cancelar</a>
            </div>
        </div>
    </div>
</div>
