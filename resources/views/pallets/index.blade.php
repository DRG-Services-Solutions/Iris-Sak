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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

            {{-- Barra de Filtros --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md p-4" x-data="{ showFilters: {{ request()->anyFilled(['status', 'location_status']) ? 'true' : 'false' }} }">
                <form method="GET" action="{{ route('pallets.index') }}">
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar código de tarima o contenedor..." 
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" @click="showFilters = !showFilters" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium text-sm border border-gray-300 dark:border-gray-600">
                                <i class="fas fa-filter mr-1"></i> Filtros
                            </button>
                            <button type="submit" class="px-4 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-500 transition font-medium text-sm shadow">
                                <i class="fas fa-search mr-1"></i> Buscar
                            </button>
                            @if(request()->anyFilled(['search', 'status', 'location_status']))
                                <a href="{{ route('pallets.index') }}" class="px-4 py-2.5 bg-slate-600 text-white rounded-lg hover:bg-slate-500 transition font-medium text-sm flex items-center">
                                    Limpiar
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Filtros Avanzados (Colapsables) --}}
                    <div x-show="showFilters" x-collapse x-cloak class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Estado de Tarima</label>
                            <select name="status" class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-teal-500 text-gray-900 dark:text-gray-100">
                                <option value="">Todos los estados</option>
                                <option value="abierta" {{ request('status') === 'abierta' ? 'selected' : '' }}>Abiertas</option>
                                <option value="cerrada" {{ request('status') === 'cerrada' ? 'selected' : '' }}>Cerradas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Asignación de Ubicación</label>
                            <select name="location_status" class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-teal-500 text-gray-900 dark:text-gray-100">
                                <option value="">Todas</option>
                                <option value="assigned" {{ request('location_status') === 'assigned' ? 'selected' : '' }}>Con Ubicación Asignada</option>
                                <option value="unassigned" {{ request('location_status') === 'unassigned' ? 'selected' : '' }}>Sin Ubicación (Pendientes)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Agrupación lógica por Contenedor --}}
            @php
                // Agrupamos las tarimas por el número de contenedor. Si no tienen, van a 'Sin Contenedor'
                $groupedPallets = $pallets->groupBy(function($pallet) {
                    return $pallet->container ? $pallet->container->container_number : 'Sin Contenedor Asignado';
                });
            @endphp

            {{-- Contenedores en Acordeón --}}
            <div class="space-y-4">
                @forelse($groupedPallets as $containerNumber => $containerPallets)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden" x-data="{ expanded: false }">
                        
                        {{-- Cabecera del Acordeón (Contenedor) --}}
                        <div @click="expanded = !expanded" class="px-6 py-4 flex items-center justify-between cursor-pointer bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors border-b border-gray-200 dark:border-gray-700">
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

                        {{-- Cuerpo del Acordeón (Tabla de Tarimas) --}}
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
                                            <tr class="hover:bg-white dark:hover:bg-gray-700/50 transition">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex flex-col">
                                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $pallet->pallet_code }}</span>
                                                        <span class="text-[10px] uppercase font-bold {{ $pallet->status === 'abierta' ? 'text-amber-500' : 'text-green-500' }}">
                                                            {{ $pallet->status }}
                                                        </span>
                                                    </div>
                                                </td>
                                                
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    @if($pallet->hasLocation())
                                                        <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400 border border-teal-200 dark:border-teal-800">
                                                            <i class="fas fa-map-marker-alt mr-1.5"></i> {{ $pallet->location->code }}
                                                        </span>
                                                        <p class="text-[10px] text-gray-500 mt-1">{{ $pallet->location->name }}</p>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                            Sin Asignar
                                                        </span>
                                                    @endif
                                                </td>
                                                
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <form action="{{ route('pallets.assign-location', $pallet) }}" method="POST" class="flex items-center justify-center space-x-2">
                                                        @csrf
                                                        <select name="location_id" class="text-xs border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-teal-500 py-1.5 w-full max-w-[220px]">
                                                            <option class="text-gray-500" value="">Seleccionar destino...</option>
                                                            @foreach($locations as $location)
                                                                @if (!$location->hasAvailableSpace() && $pallet->location_id !== $location->id)
                                                                    <option value="{{ $location->id }}" disabled class="text-red-500">
                                                                        {{ $location->code }} (Llena)
                                                                    </option>
                                                                @else
                                                                    <option value="{{ $location->id }}" {{ $pallet->location_id == $location->id ? 'selected' : '' }}>
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
                                                        <button type="submit" class="p-1.5 px-3 bg-slate-700 text-white rounded hover:bg-slate-600 transition shadow-sm font-medium text-xs">
                                                            Guardar
                                                        </button>
                                                    </form>
                                                </td>
                                                
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                    <div class="flex items-center justify-center space-x-3">
                                                        <a href="{{ route('pallets.show', $pallet) }}" class="text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition" title="Ver detalle">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
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
            </div>
        </div>
    </div>
</x-app-layout>