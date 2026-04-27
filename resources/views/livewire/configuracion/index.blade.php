<div>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Configuración</h2>
        <p class="text-sm text-gray-500 mt-0.5">Ajustes del sistema y WhatsApp</p>
    </div>

    @if($mensajeExito)
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ $mensajeExito }}</div>
    @endif

    <div class="grid grid-cols-2 gap-6 max-w-4xl">

        {{-- Datos del negocio + Logo --}}
        <div class="card space-y-4">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Datos del negocio
            </h3>

            {{-- Logo --}}
            <div>
                <label class="label-field">Logo del negocio</label>
                <div class="flex items-center gap-4">
                    @if($logoPath)
                        <img src="{{ Storage::url($logoPath) }}" alt="Logo" class="h-16 w-16 object-contain border border-gray-200 rounded-lg bg-white p-1" />
                        <div class="space-y-1">
                            <p class="text-xs text-green-600 font-medium">Logo activo</p>
                            <button wire:click="eliminarLogo" wire:confirm="¿Eliminar el logo?" class="text-xs text-red-500 hover:text-red-700">Eliminar</button>
                        </div>
                    @else
                        <div class="h-16 w-16 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50">
                            <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-xs text-gray-400">Sin logo</p>
                    @endif
                </div>

                {{-- Preview archivo seleccionado --}}
                @if($logoArchivo)
                    <div class="mt-2 flex items-center gap-3">
                        <img src="{{ $logoArchivo->temporaryUrl() }}" class="h-12 w-12 object-contain border rounded" />
                        <p class="text-xs text-indigo-600">Listo para guardar</p>
                    </div>
                @endif

                <input wire:model="logoArchivo" type="file" accept="image/*"
                    class="mt-2 block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" />
                @error('logoArchivo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-400 mt-1">PNG o JPG, máx. 2 MB. Aparecerá en el dashboard y ticket.</p>
            </div>

            <div>
                <label class="label-field">Nombre del negocio *</label>
                <input wire:model="negocioNombre" type="text" class="input-field" placeholder="Lavandería El Cisne" />
                @error('negocioNombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label-field">Teléfono</label>
                <input wire:model="negocioTelefono" type="text" class="input-field" placeholder="555 123 4567" />
                <p class="text-xs text-gray-400 mt-1">Se usa también para la prueba de WhatsApp</p>
            </div>
            <div>
                <label class="label-field">Dirección</label>
                <input wire:model="negocioDireccion" type="text" class="input-field" placeholder="Calle 5 #123, Colonia..." />
            </div>
        </div>

        {{-- EvoAPI --}}
        <div class="card">
            <h3 class="font-semibold text-gray-900 mb-1 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                WhatsApp — EvoAPI
            </h3>
            <p class="text-xs text-gray-400 mb-4">Evolution API (self-hosted). El ticket se envía como PDF adjunto.</p>

            <div class="space-y-3">
                <div>
                    <label class="label-field">URL base de EvoAPI</label>
                    <input wire:model="evoUrl" type="text" class="input-field" placeholder="https://evo.tuservidor.com" />
                    @error('evoUrl') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label-field">Nombre de instancia</label>
                    <input wire:model="evoInstancia" type="text" class="input-field" placeholder="mi-lavanderia" />
                </div>
                <div>
                    <label class="label-field">API Key</label>
                    <input wire:model="evoApikey" type="password" class="input-field" placeholder="••••••••••••" />
                </div>

                <div class="bg-blue-50 rounded-lg p-3 text-xs text-blue-700 space-y-1">
                    <p class="font-medium">Endpoint usado:</p>
                    <code class="block font-mono text-xs break-all">POST {URL}/message/sendMedia/{instancia}</code>
                    <p class="mt-1">Envía el ticket en PDF. Si falla, reintenta con texto.</p>
                </div>

                @if($mensajePrueba)
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm font-medium">{{ $mensajePrueba }}</div>
                @endif

                <button wire:click="probarWhatsapp" wire:loading.attr="disabled" class="btn-secondary text-xs w-full justify-center">
                    <svg wire:loading wire:target="probarWhatsapp" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Enviar mensaje de prueba al negocio
                </button>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <button wire:click="guardar" wire:loading.attr="disabled" class="btn-primary">
            <svg wire:loading wire:target="guardar" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <svg wire:loading.remove wire:target="guardar" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Guardar configuración
        </button>
    </div>
</div>
