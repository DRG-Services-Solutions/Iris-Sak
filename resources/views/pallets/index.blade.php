<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-teal-600 to-teal-800 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-pallet text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        Gestión de Pallets
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Control de tarimas y asignación de ubicaciones</p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <div class="text-center px-4 py-2 bg-slate-700 rounded-lg">
                    <p class="text-2xl font-bold text-white">{{ $pallets->count() }}</p>
                    <p class="text-xs text-gray-300">Total Pallets</p>
                </div>
            </div>
        </div>  
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"
             x-data="{
                search: '',
                statusFilter: '',
                locationFilter: '',
                showFilters: false,

                matchesPallet(el) {
                    const code   = (el.dataset.code   || '').toLowerCase()
                    const status = (el.dataset.status || '')
                    const hasLoc = (el.dataset.hasLocation || '')
                    const container = (el.dataset.container || '').toLowerCase()
                    const desc   = (el.dataset.description || '').toLowerCase()

                    // Búsqueda de texto
                    if (this.search) {
                        const q = this.search.toLowerCase()
                        if (!code.includes(q) && !container.includes(q) && !desc.includes(q)) return false
                    }

                    // Filtro de estado
                    if (this.statusFilter && status !== this.statusFilter) return false

                    // Filtro de ubicación
                    if (this.locationFilter === 'assigned'   && hasLoc !== '1') return false
                    if (this.locationFilter === 'unassigned' && hasLoc !== '0') return false

                    return true
                },

                visibleInGroup(groupEl) {
                    const rows = groupEl.querySelectorAll('tr[data-pallet]')
                    return Array.from(rows).some(r => this.matchesPallet(r))
                },

                get activeFilterCount() {
                    let count = 0
                    if (this.statusFilter) count++
                    if (this.locationFilter) count++
                    return count
                },

                clearAll() {
                    this.search = ''
                    this.statusFilter = ''
                    this.locationFilter = ''
                }
             }">

            {{-- Mensajes flash --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            {{-- ============================================== --}}
            {{-- BARRA DE FILTROS EN TIEMPO REAL               --}}
            {{-- ============================================== --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <div class="flex flex-col md:flex-row md:items-center gap-3">

                    {{-- Búsqueda por texto --}}
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text"
                               x-model.debounce.200ms="search"
                               placeholder="Buscar código de tarima, contenedor o descripción..."
                               class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <button x-show="search.length > 0" @click="search = ''" type="button"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>

                    {{-- Botones --}}
                    <div class="flex space-x-2">
                        <button type="button" @click="showFilters = !showFilters"
                                class="relative px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium text-sm border border-gray-300 dark:border-gray-600">
                            <i class="fas fa-filter mr-1"></i> Filtros
                            <span x-show="activeFilterCount > 0"
                                  x-text="activeFilterCount"
                                  class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-teal-600 text-white rounded-full text-[10px] font-bold flex items-center justify-center"></span>
                        </button>
                        <button x-show="search || statusFilter || locationFilter"
                                @click="clearAll()" type="button"
                                class="px-4 py-2.5 bg-slate-600 text-white rounded-lg hover:bg-slate-500 transition font-medium text-sm flex items-center">
                            <i class="fas fa-times mr-1"></i> Limpiar
                        </button>
                    </div>
                </div>

                {{-- Filtros Avanzados --}}
                <div x-show="showFilters" x-collapse x-cloak
                     class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Estado de Tarima</label>
                        <select x-model="statusFilter"
                                class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-teal-500 text-gray-900 dark:text-gray-100">
                            <option value="">Todos los estados</option>
                            <option value="abierta">Abiertas</option>
                            <option value="cerrada">Cerradas</option>
                            <option value="despachado">Embarcadas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Asignación de Ubicación</label>
                        <select x-model="locationFilter"
                                class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-teal-500 text-gray-900 dark:text-gray-100">
                            <option value="">Todas</option>
                            <option value="assigned">Con Ubicación Asignada</option>
                            <option value="unassigned">Sin Ubicación (Pendientes)</option>
                        </select>
                    </div>

                    {{-- Contador de resultados --}}
                    <div class="flex items-end">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Los filtros se aplican en tiempo real
                        </p>
                    </div>
                </div>
            </div>

            {{-- ============================================== --}}
            {{-- CONTENEDORES EN ACORDEÓN                      --}}
            {{-- ============================================== --}}
            @php
                $groupedPallets = $pallets->groupBy(function($pallet) {
                    return $pallet->container ? $pallet->container->container_seal_number : 'Sin Contenedor Asignado';
                });
            @endphp

            <div class="space-y-4">
                @forelse($groupedPallets as $containerNumber => $containerPallets)
                    {{-- Cada grupo de contenedor se oculta si no tiene pallets visibles --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden"
                         x-data="{ expanded: false }"
                         x-ref="group_{{ $loop->index }}"
                         x-show="visibleInGroup($refs.group_{{ $loop->index }})">
                        
                        {{-- Cabecera del Acordeón --}}
                        <div @click="expanded = !expanded"
                             class="px-6 py-4 flex items-center justify-between cursor-pointer bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-4">
                                <div class="bg-teal-100 dark:bg-teal-900/30 p-2.5 rounded-lg">
                                    <i class="fas fa-ship text-teal-600 dark:text-teal-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Contenedor: {{ $containerNumber }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $containerPallets->count() }} tarimas asignadas</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 text-gray-400">
                                <span class="text-xs font-medium" x-text="expanded ? 'Ocultar' : 'Ver tarimas'"></span>
                                <i class="fas fa-chevron-down transition-transform duration-300" :class="{'rotate-180': expanded}"></i>
                            </div>
                        </div>

                        {{-- Tabla de Tarimas --}}
                        <div x-show="expanded" x-collapse x-cloak>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-white dark:bg-gray-800">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/4">Código / Tarima</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/4">Ubicación Actual</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/3">Asignar Nueva Ubicación</th>
                                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50 bg-gray-50 dark:bg-gray-800/50">
                                        @foreach($containerPallets as $pallet)
                                            {{-- data-* attributes para que Alpine filtre sin tocar el DOM de Blade --}}
                                            <tr data-pallet
                                                data-code="{{ strtolower($pallet->pallet_code) }}"
                                                data-status="{{ $pallet->status }}"
                                                data-has-location="{{ $pallet->hasLocation() ? '1' : '0' }}"
                                                data-container="{{ strtolower($containerNumber) }}"
                                                data-description="{{ strtolower($pallet->boxes->pluck('containerItem.product_description')->filter()->implode(' ')) }}"
                                                x-show="matchesPallet($el)"
                                                x-data="{
                                                    isSaving: false,
                                                    hasLoc: {{ $pallet->hasLocation() ? 'true' : 'false' }},
                                                    locCode: '{{ $pallet->location?->code ?? '' }}',
                                                    locName: '{{ $pallet->location?->name ?? '' }}',
                                                    selectedLoc: '{{ $pallet->location_id ?? '' }}',
                                                    
                                                    async saveLocation() {
                                                        if(!this.selectedLoc) return;
                                                        this.isSaving = true;
                                                        try {
                                                            let res = await fetch('{{ route('pallets.assign-location', $pallet) }}', {
                                                                method: 'POST',
                                                                headers: { 
                                                                    'Content-Type': 'application/json', 
                                                                    'Accept': 'application/json', 
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                                                                },
                                                                body: JSON.stringify({ location_id: this.selectedLoc })
                                                            });
                                                            let data = await res.json();
                                                            
                                                            if(data.success) {
                                                                // Actualizamos la UI al instante
                                                                this.hasLoc = true;
                                                                this.locCode = data.location_code;
                                                                this.locName = data.location_name;
                                                                
                                                                // Actualizamos el data-attribute para que los filtros superiores sigan funcionando
                                                                $el.dataset.hasLocation = '1';
                                                            } else {
                                                                alert('Error al guardar la ubicación');
                                                            }
                                                        } catch(e) {
                                                            alert('Error de red de conexión.');
                                                        }
                                                        this.isSaving = false;
                                                    }
                                                }"
                                                class="hover:bg-white dark:hover:bg-gray-700/50 transition {{ !in_array($pallet->status, ['abierta', 'cerrada']) ? 'opacity-60' : '' }}">
                                                
                                                {{-- Código / Estado --}}
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex flex-col">
                                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $pallet->pallet_code }}</span>
                                                        @php
                                                            $statusColors = match($pallet->status) {
                                                                'abierta'   => 'text-amber-500',
                                                                'cerrada'   => 'text-green-500',
                                                                'despachado' => 'text-blue-500',
                                                                default     => 'text-gray-400',
                                                            };
                                                        @endphp
                                                        <span class="text-[10px] uppercase font-bold {{ $statusColors }}">
                                                            {{ $pallet->status }}
                                                        </span>
                                                    </div>
                                                </td>
                                                
                                                {{-- Ubicación Actual (AHORA REACTIVA) --}}
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <template x-if="hasLoc">
                                                        <div>
                                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400 border border-teal-200 dark:border-teal-800">
                                                                <i class="fas fa-map-marker-alt mr-1.5"></i> <span x-text="locCode"></span>
                                                            </span>
                                                            <p class="text-[10px] text-gray-500 mt-1" x-text="locName"></p>
                                                        </div>
                                                    </template>
                                                    <template x-if="!hasLoc">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                            Sin Asignar
                                                        </span>
                                                    </template>
                                                </td>
                                                
                                                {{-- Asignar Ubicación (AJAX FORM) --}}
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if(in_array($pallet->status, ['abierta', 'cerrada']))
                                                        <form @submit.prevent="saveLocation" class="flex items-center justify-center space-x-2">
                                                            <select x-model="selectedLoc" :disabled="isSaving" class="text-xs border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-teal-500 py-1.5 w-full max-w-[220px]">
                                                                <option class="text-gray-500" value="">Seleccionar destino...</option>
                                                                @foreach($locations as $location)
                                                                    @if (!$location->hasAvailableSpace() && $pallet->location_id !== $location->id)
                                                                        <option value="{{ $location->id }}" disabled class="text-red-500">
                                                                            {{ $location->code }} (Llena)
                                                                        </option>
                                                                    @else
                                                                        <option value="{{ $location->id }}">
                                                                            {{ $location->code }} 
                                                                            @if($location->isFloor())
                                                                                (Múltiple)
                                                                            @elseif($pallet->location_id == $location->id)
                                                                                (Actual)
                                                                            @else
                                                                                (Libre)
                                                                            @endif
                                                                        </option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                            <button type="submit" :disabled="isSaving" class="p-1.5 px-3 bg-slate-700 text-white rounded hover:bg-slate-600 transition shadow-sm font-medium text-xs disabled:opacity-50 flex items-center justify-center min-w-[70px]">
                                                                <span x-show="!isSaving">Guardar</span>
                                                                <i x-show="isSaving" class="fas fa-spinner fa-spin"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                                            <i class="fas fa-lock mr-1.5"></i> No disponible
                                                        </span>
                                                    @endif
                                                </td>
                                                
                                                {{-- Acciones --}}
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                    <div class="flex items-center justify-center space-x-3">
                                                        <a href="{{ route('pallets.show', $pallet) }}" class="text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition" title="Ver detalle">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if(in_array($pallet->status, ['abierta', 'cerrada']))
                                                            <a href="{{ route('pallets.edit', $pallet) }}" class="text-teal-500 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-200 transition" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('pallets.destroy', $pallet) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar esta tarima permanentemente?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-400 hover:text-red-600 dark:hover:text-red-300 transition" title="Eliminar">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-pallet text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">No se encontraron pallets con los filtros actuales.</p>
                        </div>
                    </div>
                @endforelse

                {{-- Mensaje cuando los filtros no arrojan resultados --}}
                <div x-show="document.querySelectorAll('[x-ref^=group_]').length > 0 && ![...document.querySelectorAll('[x-ref^=group_]')].some(g => visibleInGroup(g))"
                     x-cloak
                     class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-filter text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Ninguna tarima coincide con los filtros aplicados.</p>
                        <button @click="clearAll()" class="mt-3 text-sm text-teal-600 hover:text-teal-700 font-medium">
                            <i class="fas fa-times mr-1"></i> Limpiar filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>