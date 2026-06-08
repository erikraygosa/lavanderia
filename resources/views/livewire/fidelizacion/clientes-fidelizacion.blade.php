<div>

    {{-- ══════════════════════════════════════════════════════════
         TABS
    ══════════════════════════════════════════════════════════ --}}
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-6">
            <button wire:click="$set('tab', 'clientes')"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors
                           {{ $tab === 'clientes'
                               ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
                               : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                Clientes
                <span class="ml-1.5 rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs text-gray-600 dark:text-gray-300">
                    {{ array_sum($conteos) }}
                </span>
            </button>
            <button wire:click="$set('tab', 'historial')"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors
                           {{ $tab === 'historial'
                               ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
                               : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                Historial de envíos
            </button>
        </nav>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         TAB: CLIENTES
    ══════════════════════════════════════════════════════════ --}}
    @if($tab === 'clientes')

        {{-- Cards métricas --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

            {{-- NUEVO --}}
            <button wire:click="filtrarPor('NUEVO')"
                    class="text-left rounded-xl border p-4 transition-all
                           {{ $filtroEstado === 'NUEVO'
                               ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 ring-2 ring-blue-400'
                               : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-blue-300 dark:hover:border-blue-600' }}">
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $conteos['NUEVO'] }}</p>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-0.5">Nuevos</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">1er pedido &lt; 7 días</p>
            </button>

            {{-- ACTIVO --}}
            <button wire:click="filtrarPor('ACTIVO')"
                    class="text-left rounded-xl border p-4 transition-all
                           {{ $filtroEstado === 'ACTIVO'
                               ? 'border-green-500 bg-green-50 dark:bg-green-900/30 ring-2 ring-green-400'
                               : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-green-300 dark:hover:border-green-600' }}">
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $conteos['ACTIVO'] }}</p>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-0.5">Activos</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">2+ pedidos, &lt; 21 días</p>
            </button>

            {{-- EN RIESGO --}}
            <button wire:click="filtrarPor('EN_RIESGO')"
                    class="text-left rounded-xl border p-4 transition-all
                           {{ $filtroEstado === 'EN_RIESGO'
                               ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/30 ring-2 ring-amber-400'
                               : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-amber-300 dark:hover:border-amber-600' }}">
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $conteos['EN_RIESGO'] }}</p>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-0.5">En riesgo</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">21–40 días sin venir</p>
            </button>

            {{-- INACTIVO --}}
            <button wire:click="filtrarPor('INACTIVO')"
                    class="text-left rounded-xl border p-4 transition-all
                           {{ $filtroEstado === 'INACTIVO'
                               ? 'border-red-500 bg-red-50 dark:bg-red-900/30 ring-2 ring-red-400'
                               : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-red-300 dark:hover:border-red-600' }}">
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $conteos['INACTIVO'] }}</p>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-0.5">Inactivos</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">+40 días o sin pedidos</p>
            </button>

        </div>

        {{-- Buscador y filtro --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.400ms="buscar"
                           type="text"
                           placeholder="Buscar por nombre o teléfono..."
                           class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <select wire:model.live="filtroEstado"
                        class="text-sm rounded-lg border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                               px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Todos los estados</option>
                    <option value="NUEVO">Nuevo</option>
                    <option value="ACTIVO">Activo</option>
                    <option value="EN_RIESGO">En riesgo</option>
                    <option value="INACTIVO">Inactivo</option>
                </select>

                @if($filtroEstado || $buscar)
                <button wire:click="$set('filtroEstado', ''); $set('buscar', '')"
                        class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors whitespace-nowrap">
                    Limpiar filtros
                </button>
                @endif
            </div>
        </div>

        {{-- Tabla --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Teléfono</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Último pedido</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pedidos</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ticket prom.</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($paginator as $item)
                        @php
                            $colorText = match($item['estado']) {
                                'NUEVO'     => 'text-blue-600 dark:text-blue-400',
                                'ACTIVO'    => 'text-green-600 dark:text-green-400',
                                'EN_RIESGO' => 'text-amber-600 dark:text-amber-400',
                                default     => 'text-red-600 dark:text-red-400',
                            };
                            $badgeCss = match($item['estado']) {
                                'NUEVO'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                'ACTIVO'    => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                                'EN_RIESGO' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                default     => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                            };
                            $estadoLabel = match($item['estado']) {
                                'NUEVO'     => 'Nuevo',
                                'ACTIVO'    => 'Activo',
                                'EN_RIESGO' => 'En riesgo',
                                default     => 'Inactivo',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $item['cliente']->nombre }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item['cliente']->telefono ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($item['ultimo_pedido'])
                                    <span class="text-sm {{ $colorText }}">
                                        {{ $item['ultimo_pedido']->locale('es')->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">Nunca</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $item['total_pedidos'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    ${{ number_format($item['ticket_promedio'], 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeCss }}">
                                    {{ $estadoLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('fidelizacion.mensaje', $item['cliente']->id) }}"
                                   class="inline-flex items-center gap-1.5 text-xs bg-indigo-600 hover:bg-indigo-700
                                          text-white px-3 py-1.5 rounded-lg font-medium transition-colors">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    Enviar WA
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-400 dark:text-gray-500">
                                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-sm">No se encontraron clientes con ese filtro.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($paginator->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                {{ $paginator->links() }}
            </div>
            @endif
        </div>

    @endif {{-- fin tab clientes --}}


    {{-- ══════════════════════════════════════════════════════════
         TAB: HISTORIAL DE ENVÍOS
    ══════════════════════════════════════════════════════════ --}}
    @if($tab === 'historial')

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Últimos 50 envíos de WhatsApp</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha / Hora</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cliente</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mensaje</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Resultado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($historial as $log)
                        @php
                            $logBadge = match($log->estado_cliente) {
                                'NUEVO'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                'ACTIVO'    => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                                'EN_RIESGO' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                default     => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                            };
                            $logLabel = match($log->estado_cliente) {
                                'NUEVO'     => 'Nuevo',
                                'ACTIVO'    => 'Activo',
                                'EN_RIESGO' => 'En riesgo',
                                default     => 'Inactivo',
                            };
                            $enviado = $log->respuesta_evo === 'OK';
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($log->enviado_at)->timezone('America/Merida')->format('d/m/Y H:i') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $log->cliente?->nombre ?? '(eliminado)' }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $log->telefono }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $logBadge }}">
                                    {{ $logLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                <p class="text-xs text-gray-600 dark:text-gray-400 truncate" title="{{ $log->mensaje_enviado }}">
                                    {{ \Illuminate\Support\Str::limit($log->mensaje_enviado, 60) }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($enviado)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 dark:text-green-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Enviado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-red-600 dark:text-red-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Error
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <p class="text-sm text-gray-400 dark:text-gray-500">Aún no se ha enviado ningún mensaje.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @endif {{-- fin tab historial --}}



</div>
