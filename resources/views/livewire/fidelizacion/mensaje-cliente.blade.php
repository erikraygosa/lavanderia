<div>
    @php
        $estado = $clasificacion['estado'] ?? 'INACTIVO';

        $badgeCss = match($estado) {
            'NUEVO'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
            'ACTIVO'    => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
            'EN_RIESGO' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
            default     => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        };
        $colorDias = match($estado) {
            'NUEVO'     => 'text-blue-600 dark:text-blue-400',
            'ACTIVO'    => 'text-green-600 dark:text-green-400',
            'EN_RIESGO' => 'text-amber-600 dark:text-amber-400',
            default     => 'text-red-600 dark:text-red-400',
        };
        $estadoLabel = match($estado) {
            'NUEVO'     => 'Nuevo',
            'ACTIVO'    => 'Activo',
            'EN_RIESGO' => 'En riesgo',
            default     => 'Inactivo',
        };
    @endphp

    {{-- Cabecera con botón volver --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('fidelizacion.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400
                  hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a Fidelización
        </a>
    </div>

    <div class="grid lg:grid-cols-5 gap-6">

        {{-- ══ Columna izquierda: ficha del cliente ══ --}}
        <div class="lg:col-span-2 space-y-4">

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                    {{ $cliente->nombre }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $cliente->telefono ?? 'Sin teléfono' }}
                </p>

                <div class="mt-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badgeCss }}">
                        {{ $estadoLabel }}
                    </span>
                </div>

                <div class="mt-4 space-y-0 text-sm divide-y divide-gray-100 dark:divide-gray-700">
                    <div class="flex justify-between items-center py-2.5">
                        <span class="text-gray-500 dark:text-gray-400">Días sin venir</span>
                        <span class="font-semibold {{ $colorDias }}">
                            {{ ($clasificacion['dias_sin_venir'] ?? 9999) >= 9999
                                ? 'Nunca ha venido'
                                : $clasificacion['dias_sin_venir'] . ' días' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2.5">
                        <span class="text-gray-500 dark:text-gray-400">Total pedidos</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $clasificacion['total_pedidos'] ?? 0 }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2.5">
                        <span class="text-gray-500 dark:text-gray-400">Ticket promedio</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($clasificacion['ticket_promedio'] ?? 0, 2) }}
                        </span>
                    </div>
                    @if(!empty($clasificacion['ultimo_pedido_str']))
                    <div class="flex justify-between items-center py-2.5">
                        <span class="text-gray-500 dark:text-gray-400">Último pedido</span>
                        <span class="text-gray-700 dark:text-gray-300 text-xs">
                            {{ \Carbon\Carbon::parse($clasificacion['ultimo_pedido_str'])->locale('es')->diffForHumans() }}
                        </span>
                    </div>
                    @endif
                </div>

                @if(!empty($clasificacion['servicios_frecuentes']))
                <div class="mt-4">
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
                        Servicios frecuentes
                    </p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($clasificacion['servicios_frecuentes'] as $svc)
                        <span class="inline-block bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300
                                     text-xs px-2 py-0.5 rounded-full">
                            {{ $svc }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

        </div>

        {{-- ══ Columna derecha: mensaje ══ --}}
        <div class="lg:col-span-3 space-y-4">

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Mensaje a enviar
                    <span class="normal-case font-normal text-gray-400 ml-1">(editable)</span>
                </label>

                <textarea wire:model="mensajeEditable"
                          rows="10"
                          class="w-full text-sm rounded-lg border border-gray-300 dark:border-gray-600
                                 bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                 resize-none leading-relaxed p-3">
                </textarea>
            </div>

            {{-- Alerta --}}
            @if($alertaTipo)
            <div class="rounded-xl px-4 py-3 text-sm font-medium
                        {{ $alertaTipo === 'success'
                            ? 'bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-700'
                            : 'bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-700' }}">
                {{ $alertaMensaje }}
            </div>
            @endif

            {{-- Botones de acción --}}
            <div x-data="{ bloqueado: false }"
                 @abrirUrl.window="window.open($event.detail.url, '_blank')"
                 class="flex flex-wrap gap-3">

                {{-- Enviar por WhatsApp --}}
                <button wire:click="enviarWhatsApp"
                        :disabled="bloqueado || $wire.enviando"
                        @click="bloqueado = true; setTimeout(() => bloqueado = false, 3000)"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3
                               bg-green-600 hover:bg-green-700 disabled:bg-green-400 disabled:cursor-not-allowed
                               text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                    <svg x-show="!$wire.enviando" class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    <svg x-show="$wire.enviando" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="$wire.enviando ? 'Enviando...' : (bloqueado ? 'Enviado ✓' : 'Enviar por WhatsApp')"></span>
                </button>

                {{-- Abrir WhatsApp Web --}}
                <button wire:click="abrirWhatsAppWeb"
                        class="inline-flex items-center gap-2 px-4 py-3
                               bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                               text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition-colors"
                        title="Abrir en WhatsApp Web">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    WhatsApp Web
                </button>

                {{-- Copiar mensaje --}}
                <button x-data="{ copiado: false }"
                        @click="navigator.clipboard.writeText($wire.mensajeEditable)
                                    .then(() => { copiado = true; setTimeout(() => copiado = false, 2000) })"
                        class="inline-flex items-center gap-2 px-4 py-3
                               bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                               text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition-colors">
                    <svg x-show="!copiado" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <svg x-show="copiado" class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="copiado ? 'Copiado ✓' : 'Copiar'"></span>
                </button>

            </div>

        </div>

    </div>

</div>
