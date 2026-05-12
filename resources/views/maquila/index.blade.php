<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-pink-600 to-rose-700 p-3 rounded-lg shadow-lg"><i class="fas fa-cogs text-white text-xl"></i></div>
                <div><h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Maquila</h2><p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Control de estaciones por contenedor</p></div>
            </div>
            <a href="{{ route('maquila.logs') }}" class="hidden md:inline-flex px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 text-sm"><i class="fas fa-history mr-1"></i> Historial</a>
        </div>
    </x-slot>
    
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))<div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif
            
            {{-- Formulario de Filtros --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700" x-data="{ showFilters: {{ request()->anyFilled(['container', 'pallet', 'article', 'date_from', 'date_to']) ? 'true' : 'false' }} }">
                <div class="px-5 py-3 flex items-center justify-between cursor-pointer bg-gray-50 dark:bg-gray-700/50 rounded-t-lg" @click="showFilters = !showFilters">
                    <div class="flex items-center space-x-2 text-gray-700 dark:text-gray-200 font-semibold text-sm">
                        <i class="fas fa-filter text-pink-600"></i>
                        <span>Filtros de Búsqueda</span>
                        @if(request()->anyFilled(['container', 'pallet', 'article', 'date_from', 'date_to']))
                            <span class="px-2 py-0.5 bg-pink-100 text-pink-700 rounded-full text-xs ml-2">Filtros activos</span>
                        @endif
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" :class="{'rotate-180': showFilters}"></i>
                </div>
                
                <div x-show="showFilters" x-collapse>
                    <form method="GET" action="{{ route('maquila.index') }}" class="p-5">
                        @if(request('station'))
                            <input type="hidden" name="station" value="{{ request('station') }}">
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">ID Contenedor</label>
                                <input type="text" name="container" value="{{ request('container') }}" class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm focus:ring-pink-500 focus:border-pink-500 dark:text-white" placeholder="Ej. ID Contenedor...">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Tarima</label>
                                <input type="text" name="pallet" value="{{ request('pallet') }}" class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm focus:ring-pink-500 focus:border-pink-500 dark:text-white" placeholder="Ej. TAR-...">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Artículo (SKU / Barcode)</label>
                                <input type="text" name="article" value="{{ request('article') }}" class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm focus:ring-pink-500 focus:border-pink-500 dark:text-white" placeholder="Código o descripción">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Desde</label>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm focus:ring-pink-500 focus:border-pink-500 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Hasta</label>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm focus:ring-pink-500 focus:border-pink-500 dark:text-white">
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end space-x-3">
                            @if(request()->anyFilled(['container', 'pallet', 'article', 'date_from', 'date_to', 'station']))
                                <a href="{{ route('maquila.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                    Limpiar
                                </a>
                            @endif
                            <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm hover:bg-pink-500 transition shadow">
                                <i class="fas fa-search mr-1"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- KPIs --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center border-b-4 border-gray-300"><p class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $stationCounts['sin_iniciar'] }}</p><p class="text-xs text-gray-500">Sin iniciar</p></div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center border-b-4 border-amber-400"><p class="text-2xl font-bold text-amber-600">{{ $stationCounts[1] }}</p><p class="text-xs text-gray-500">Estación 1</p></div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center border-b-4 border-blue-400"><p class="text-2xl font-bold text-blue-600">{{ $stationCounts[2] }}</p><p class="text-xs text-gray-500">Estación 2</p></div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center border-b-4 border-purple-400"><p class="text-2xl font-bold text-purple-600">{{ $stationCounts[3] }}</p><p class="text-xs text-gray-500">Estación 3</p></div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center border-b-4 border-green-400"><p class="text-2xl font-bold text-green-600">{{ $stationCounts['completado'] }}</p><p class="text-xs text-gray-500">Completado</p></div>
            </div>

            <div class="md:hidden"><a href="{{ route('maquila.logs') }}" class="flex items-center justify-center w-full px-4 py-2.5 bg-slate-700 text-white rounded-lg text-sm"><i class="fas fa-history mr-2"></i> Historial</a></div>

            {{-- Contenedores --}}
            @forelse($containers as $container)
                @php
                    $palletsInContainer = $container->pallets;
                    
                    if ($stationFilter === 'sin_iniciar') { $palletsInContainer = $palletsInContainer->filter(fn($p) => $p->maquila_started_at === null); }
                    elseif ($stationFilter === 'completado') { $palletsInContainer = $palletsInContainer->filter(fn($p) => $p->maquila_completed_at !== null); }
                    elseif (is_numeric($stationFilter)) { $palletsInContainer = $palletsInContainer->filter(fn($p) => $p->maquila_station == $stationFilter && $p->maquila_completed_at === null); }
                    
                    if ($palletsInContainer->isEmpty() && $stationFilter) { continue; }
                @endphp
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden" x-data="{ open: true }">
                    {{-- Cabecera del Contenedor --}}
                    <div class="px-5 py-4 flex items-center justify-between cursor-pointer border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50" @click="open = !open">
                        <div class="flex items-center space-x-3">
                            <div class="bg-slate-100 dark:bg-slate-700 p-2 rounded-lg"><i class="fas fa-shipping-fast text-slate-600 dark:text-slate-300"></i></div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Contenedor: {{ $container->container_seal_number }}</h3>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @php
                                $locE1 = $container->pallets->filter(fn($p) => $p->maquila_station === 1 && !$p->maquila_completed_at)->count();
                                $locE2 = $container->pallets->filter(fn($p) => $p->maquila_station === 2 && !$p->maquila_completed_at)->count();
                                $locE3 = $container->pallets->filter(fn($p) => $p->maquila_station === 3 && !$p->maquila_completed_at)->count();
                                $locDone = $container->pallets->filter(fn($p) => $p->maquila_completed_at !== null)->count();
                            @endphp
                            @if($locE1)<span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">E1: {{ $locE1 }}</span>@endif
                            @if($locE2)<span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">E2: {{ $locE2 }}</span>@endif
                            @if($locE3)<span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">E3: {{ $locE3 }}</span>@endif
                            @if($locDone)<span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-bold"><i class="fas fa-check mr-0.5"></i>{{ $locDone }}</span>@endif
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    
                    {{-- Lista de Tarimas --}}
                    <div x-show="open" x-collapse>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($palletsInContainer->sortBy('pallet_code') as $pallet)
                                @php
                                    $stationBadge = match($pallet->maquila_station) { 
                                        1 => 'bg-amber-100 text-amber-800 border-amber-300', 
                                        2 => 'bg-blue-100 text-blue-800 border-blue-300', 
                                        3 => 'bg-purple-100 text-purple-800 border-purple-300', 
                                        default => 'bg-gray-100 text-gray-600 border-gray-300' 
                                    };
                                    $isCompleted = $pallet->maquila_completed_at !== null;
                                @endphp
                                <div class="px-5 py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-3 {{ $isCompleted ? 'bg-green-50/50 dark:bg-green-900/5' : '' }} hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <div class="flex items-center space-x-3 flex-1">
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <p class="font-mono font-bold text-sm text-gray-900 dark:text-white">{{ $pallet->pallet_code }}</p>
                                                
                                                {{-- BADGE DE INCIDENCIA VISUAL --}}
                                                @if($pallet->maquila_status && $pallet->maquila_status == 'normal')
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 border border-green-200 dark:bg-green-900/30 dark:border-green-800 dark:text-green-400 rounded text-[10px] font-bold uppercase shadow-sm">

                                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $pallet->maquila_status }}
                                                    </span>
                                                @elseif ($pallet->maquila_status !== 'normal')
                                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 border border-red-200 dark:bg-red-900/30 dark:border-red-800 dark:text-red-400 rounded text-[10px] font-bold uppercase shadow-sm">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $pallet->maquila_status }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <p class="text-xs text-gray-500 mt-1">
                                                @if($pallet->location)
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium mr-1"><i class="fas fa-map-marker-alt"></i> {{ $pallet->location->code }}</span> 
                                                @else
                                                    <span class="text-red-500 font-medium mr-1"><i class="fas fa-exclamation-triangle"></i> Sin ubicación</span> 
                                                @endif
                                                · {{ $pallet->boxes->count() }} cajas · {{ number_format($pallet->boxes->sum('quantity')) }} pzas
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        @if($isCompleted)
                                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 border border-green-300 rounded-full text-xs font-bold"><i class="fas fa-check-circle mr-1"></i> Completado</span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 {{ $stationBadge }} border rounded-full text-xs font-bold">{{ $pallet->maquila_station ? 'Estación ' . $pallet->maquila_station : 'Sin asignar' }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 flex-shrink-0">
                                        @if(!$isCompleted)
                                            
                                            {{-- BOTÓN PARA ABRIR MODAL DE INCIDENCIA --}}
                                            <button type="button" title="Reportar Incidencia" 
                                                @click="$dispatch('open-incident-modal', { id: {{ $pallet->id }}, code: '{{ $pallet->pallet_code }}', status: '{{ $pallet->maquila_status ?? 'disponibles' }}' })"
                                                class="px-2 py-1.5 rounded-md text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition text-sm">
                                                <i class="fas fa-flag"></i>
                                            </button>

                                            <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-0.5">
                                                @foreach([1, 2, 3] as $st)
                                                    @php
                                                        $isCurrentStation = $pallet->maquila_station === $st;
                                                        $isStationOccupied = $stationCounts[$st] > 0; 
                                                        $isDisabled = $isCurrentStation || $isStationOccupied;
                                                    @endphp
                                                    <form method="POST" action="{{ route('maquila.move', $pallet) }}" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="station" value="{{ $st }}">
                                                        <button type="submit" 
                                                                title="{{ $isCurrentStation ? 'Estación actual' : ($isStationOccupied ? 'Estación Ocupada' : 'Mover a E'.$st) }}"
                                                                class="px-3 py-1.5 text-xs font-bold rounded-md transition 
                                                                {{ $isCurrentStation ? match($st) { 1 => 'bg-amber-500 text-white shadow', 2 => 'bg-blue-500 text-white shadow', 3 => 'bg-purple-500 text-white shadow' } : 
                                                                ($isStationOccupied ? 'text-gray-300 dark:text-gray-600 cursor-not-allowed' : 'text-gray-500 hover:bg-white dark:hover:bg-gray-600 hover:shadow') }}" 
                                                                {{ $isDisabled ? 'disabled' : '' }}>
                                                            E{{ $st }}
                                                        </button>
                                                    </form>
                                                @endforeach
                                            </div>
                                            @if($pallet->maquila_station !== null)
                                                <form method="POST" action="{{ route('maquila.complete', $pallet) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-medium hover:bg-green-500 transition"><i class="fas fa-check mr-1"></i> Completar</button>
                                                </form>
                                            @endif
                                        @else
                                            {{-- También permitimos reportar incidencia aunque esté completada por si hay ajustes post-maquila --}}
                                            <button type="button" title="Reportar Incidencia" 
                                                @click="$dispatch('open-incident-modal', { id: {{ $pallet->id }}, code: '{{ $pallet->pallet_code }}', status: '{{ $pallet->maquila_status ?? 'disponibles' }}' })"
                                                class="px-2 py-1.5 rounded-md text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition text-sm">
                                                <i class="fas fa-flag"></i>
                                            </button>

                                            <a href="{{ route('maquila.print-label', $pallet) }}" target="_blank" class="px-3 py-1.5 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-xs font-medium transition shadow-sm">
                                                <i class="fas fa-print mr-1"></i> Imprimir Etiqueta
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
                    <i class="fas fa-shipping-fast text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 font-medium">No hay contenedores con tarimas cerradas</p>
                    <p class="text-sm text-gray-400 mt-1">Cierre contenedores y arme tarimas en el módulo de recepción</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL DE INCIDENCIAS (GLOBAL PARA LA VISTA)              --}}
    {{-- ======================================================== --}}
    <div x-data="{
            open: false,
            palletId: null,
            palletCode: '',
            status: 'disponibles',
            openModal(e) {
                this.palletId = e.detail.id;
                this.palletCode = e.detail.code;
                this.status = e.detail.status || 'disponibles';
                this.open = true;
            }
        }"
        @open-incident-modal.window="openModal($event)"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        {{-- Fondo oscuro --}}
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-900/75 dark:bg-gray-900/90 transition-opacity"></div>

        {{-- Contenedor del Modal --}}
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.outside="open = false"
             class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md p-6 overflow-hidden mx-4">
            
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">
                    <i class="fas fa-flag text-red-500 mr-2"></i> Reportar Incidencia
                </h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Actualizando estatus para la tarima: <span class="font-mono font-bold text-gray-800 dark:text-gray-200" x-text="palletCode"></span>
            </p>

            <form x-bind:action="'{{ url('maquila/status') }}/' + palletId" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tipo de Incidencia</label>
                    <select name="maquila_status" x-model="status" required class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-pink-500 focus:border-pink-500 dark:text-white py-2.5">
                        <option value="disponibles">Sin incidencias (Disponibles)</option>
                        <option value="merma">Merma (Daño)</option>
                        <option value="faltante">Faltante físico</option>
                        <option value="sobrante">Sobrante físico</option>
                        <option value="codigo">Error en Código</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" @click="open = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-500 transition shadow">Guardar Estatus</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>w