<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-br from-cyan-600 to-blue-800 p-3 rounded-lg shadow-lg">
                <i class="fas fa-boxes text-white text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">Inventario de Existencias</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Visor jerárquico y búsqueda en tiempo real</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8" 
         x-data="{
            search: '',
            dateFrom: '',
            dateTo: '',

            // Lógica de filtrado ultrarrápida
            matchesBox(el) {
                const searchString = (
                    (el.dataset.boxCode || '') + ' ' + 
                    (el.dataset.sku || '') + ' ' + 
                    (el.dataset.pallet || '') + ' ' + 
                    (el.dataset.container || '')
                ).toLowerCase();
                
                const boxDate = el.dataset.date || '';

                // Filtro de Texto
                if (this.search && !searchString.includes(this.search.toLowerCase())) {
                    return false;
                }

                // Filtro de Fechas
                if (this.dateFrom && boxDate < this.dateFrom) return false;
                if (this.dateTo && boxDate > this.dateTo) return false;

                return true;
            },

            // Verifica si un contenedor o tarima debe mostrarse
            visibleInWrapper(wrapperEl) {
                const rows = wrapperEl.querySelectorAll('tr[data-box-row]');
                return Array.from(rows).some(r => this.matchesBox(r));
            },

            clearFilters() {
                this.search = '';
                this.dateFrom = '';
                this.dateTo = '';
            }
         }">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- KPIs Resumen --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center border-b-4 border-cyan-500">
                    <p class="text-2xl font-black text-cyan-600 dark:text-cyan-400">{{ number_format($stats['total_piezas']) }}</p>
                    <p class="text-xs font-bold text-gray-500 uppercase mt-1">Piezas Físicas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center border-b-4 border-blue-500">
                    <p class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ number_format($stats['total_cajas']) }}</p>
                    <p class="text-xs font-bold text-gray-500 uppercase mt-1">Cajas Totales</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center border-b-4 border-indigo-500">
                    <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ number_format($stats['total_tarimas']) }}</p>
                    <p class="text-xs font-bold text-gray-500 uppercase mt-1">Tarimas Armadas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center border-b-4 border-purple-500">
                    <p class="text-2xl font-black text-purple-600 dark:text-purple-400">{{ number_format($stats['total_contenedores']) }}</p>
                    <p class="text-xs font-bold text-gray-500 uppercase mt-1">Contenedores</p>
                </div>
            </div>

            {{-- Filtros en Tiempo Real --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                    <div class="md:col-span-6 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" x-model.debounce.300ms="search" placeholder="Filtrar por caja, SKU, tarima o contenedor..." class="w-full pl-10 pr-4 py-2 border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-cyan-500 focus:border-cyan-500 dark:text-white font-mono">
                    </div>
                    <div class="md:col-span-2">
                        <input type="date" x-model="dateFrom" class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-cyan-500 dark:text-white" title="Fecha de Ingreso Desde">
                    </div>
                    <div class="md:col-span-2">
                        <input type="date" x-model="dateTo" class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-cyan-500 dark:text-white" title="Fecha de Ingreso Hasta">
                    </div>
                    <div class="md:col-span-2">
                        <button @click="clearFilters()" x-show="search || dateFrom || dateTo" x-cloak type="button" class="w-full px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600 transition text-sm font-medium">
                            <i class="fas fa-eraser mr-1"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>

            {{-- VISTA JERÁRQUICA --}}
            @if($boxes->count())
                @php
                    // Agrupación Nivel 1: Contenedor
                    $groupedByContainer = $boxes->groupBy(fn($b) => $b->container?->container_seal_number ?? 'Sin Contenedor');
                @endphp

                <div class="space-y-3">
                    @foreach($groupedByContainer as $containerSeal => $boxesInContainer)
                        
                        {{-- NIVEL 1: Contenedor --}}
                        <div x-data="{ openCont: false }" x-ref="cont_{{ $loop->index }}" x-show="visibleInWrapper($refs.cont_{{ $loop->index }})" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            
                            <div @click="openCont = !openCont" class="px-5 py-3 bg-slate-50 dark:bg-slate-800/80 flex items-center justify-between cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-ship text-slate-500"></i>
                                    <div>
                                        <h3 class="font-bold text-gray-800 dark:text-white text-sm">Contenedor: {{ $containerSeal }}</h3>
                                        <p class="text-[10px] text-gray-500">{{ $boxesInContainer->count() }} cajas · {{ number_format($boxesInContainer->sum('quantity')) }} piezas</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200 text-sm" :class="{'rotate-180': openCont}"></i>
                            </div>

                            <div x-show="openCont" x-collapse x-cloak class="p-3 bg-gray-50/50 dark:bg-gray-900/20 space-y-3 border-t border-gray-200 dark:border-gray-700">
                                @php
                                    // Agrupación Nivel 2: Tarima
                                    $groupedByPallet = $boxesInContainer->groupBy(fn($b) => $b->pallet?->pallet_code ?? 'Sueltas / Sin Tarima');
                                @endphp

                                @foreach($groupedByPallet as $palletCode => $boxesInPallet)
                                    @php
                                        $palletLocation = $boxesInPallet->first()->pallet?->location?->code ?? 'Sin ubicación';
                                    @endphp

                                    {{-- NIVEL 2: Tarima --}}
                                    <div x-data="{ openPallet: false }" x-ref="pal_{{ $loop->parent->index }}_{{ $loop->index }}" x-show="visibleInWrapper($refs.pal_{{ $loop->parent->index }}_{{ $loop->index }})" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden shadow-sm">
                                        <div @click="openPallet = !openPallet" class="px-4 py-2.5 flex items-center justify-between cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <div class="flex items-center space-x-3">
                                                <i class="fas fa-pallet text-cyan-600"></i>
                                                <div>
                                                    <span class="font-mono font-bold text-gray-800 dark:text-gray-200 text-sm">{{ $palletCode }}</span>
                                                    <span class="ml-2 text-[10px] bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 px-2 py-0.5 rounded font-medium"><i class="fas fa-map-marker-alt mr-1"></i>{{ $palletLocation }}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="text-xs font-semibold text-gray-500">{{ $boxesInPallet->count() }} cajas</span>
                                                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200 text-sm" :class="{'rotate-180': openPallet}"></i>
                                            </div>
                                        </div>

                                        {{-- NIVEL 3: Cajas y Piezas (Tabla) --}}
                                        <div x-show="openPallet" x-collapse x-cloak class="overflow-x-auto border-t border-gray-200 dark:border-gray-700">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-[10px] font-bold text-gray-500 uppercase">Caja</th>
                                                        <th class="px-4 py-2 text-left text-[10px] font-bold text-gray-500 uppercase">SKU</th>
                                                        <th class="px-4 py-2 text-center text-[10px] font-bold text-gray-500 uppercase">Piezas</th>
                                                        <th class="px-4 py-2 text-center text-[10px] font-bold text-gray-500 uppercase">Ingreso</th>
                                                        <th class="px-4 py-2 text-center text-[10px] font-bold text-gray-500 uppercase">Estatus</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                                    @foreach($boxesInPallet as $box)
                                                        <tr data-box-row 
                                                            data-box-code="{{ strtolower($box->box_code) }}" 
                                                            data-sku="{{ strtolower($box->containerItem->barcode ?? '') }}" 
                                                            data-pallet="{{ strtolower($palletCode) }}" 
                                                            data-container="{{ strtolower($containerSeal) }}" 
                                                            data-date="{{ $box->created_at->format('Y-m-d') }}"
                                                            x-show="matchesBox($el)"
                                                            class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                                            
                                                            <td class="px-4 py-2 whitespace-nowrap">
                                                                <span class="text-xs font-mono font-bold text-gray-900 dark:text-white">{{ $box->box_code }}</span>
                                                            </td>
                                                            <td class="px-4 py-2 whitespace-nowrap">
                                                                <span class="text-xs font-mono text-gray-600 dark:text-gray-300">{{ $box->containerItem->barcode ?? 'N/A' }}</span>
                                                            </td>
                                                            <td class="px-4 py-2 whitespace-nowrap text-center">
                                                                <span class="text-xs font-bold text-cyan-600 dark:text-cyan-400">{{ $box->quantity }}</span>
                                                            </td>
                                                            <td class="px-4 py-2 whitespace-nowrap text-center">
                                                                <span class="text-[10px] text-gray-500">{{ $box->created_at->format('d/m/Y') }}</span>
                                                            </td>
                                                            <td class="px-4 py-2 whitespace-nowrap text-center">
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                                                    {{ str_replace('_', ' ', $box->status) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Estado Vacío --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center border border-gray-200 dark:border-gray-700">
                    <i class="fas fa-warehouse text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500 font-medium text-lg">Inventario Vacío</p>
                    <p class="text-sm text-gray-400 mt-1">No hay cajas disponibles en el almacén en este momento.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>