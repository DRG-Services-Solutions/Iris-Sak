<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-pink-600 to-rose-800 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-route text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">Trazabilidad de Inventario</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ciclo de vida caja por caja</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            {{-- STATS --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total_cajas'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total Cajas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $stats['embarcadas'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Embarcadas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['en_almacen'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">En Almacen</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['promedio_dias'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Promedio Dias</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">{{ $stats['max_dias'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Max. Dias</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['sin_tarima'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Sin Tarima</p>
                </div>
            </div>

            {{-- FILTROS --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4" x-data="{ showFilters: {{ !empty(array_filter($filters ?? [])) ? 'true' : 'false' }} }">
                <form method="GET" action="{{ route('reports.storage-time') }}">
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                                   placeholder="Buscar codigo de caja, tarima, barcode..."
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" @click="showFilters = !showFilters"
                                    class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium text-sm border border-gray-300 dark:border-gray-600">
                                <i class="fas fa-filter mr-1"></i> Filtros
                            </button>
                            <button type="submit" class="px-4 py-2.5 bg-pink-600 text-white rounded-lg hover:bg-pink-500 transition font-medium text-sm shadow">
                                <i class="fas fa-search mr-1"></i> Buscar
                            </button>
                            @if(!empty(array_filter($filters ?? [])))
                                <a href="{{ route('reports.storage-time') }}" class="px-4 py-2.5 bg-slate-600 text-white rounded-lg hover:bg-slate-500 transition font-medium text-sm flex items-center">
                                    Limpiar
                                </a>
                            @endif
                        </div>
                    </div>

                    <div x-show="showFilters" x-collapse x-cloak class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Contenedor</label>
                            <select name="container_id" class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-pink-500 text-gray-900 dark:text-gray-100">
                                <option value="">Todos</option>
                                @foreach($containers as $container)
                                    <option value="{{ $container->id }}" {{ ($filters['container_id'] ?? '') == $container->id ? 'selected' : '' }}>
                                        {{ $container->container_seal_number }} ({{ $container->container_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Estado</label>
                            <select name="status" class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-pink-500 text-gray-900 dark:text-gray-100">
                                <option value="todos" {{ ($filters['status'] ?? '') === 'todos' ? 'selected' : '' }}>Todos</option>
                                <option value="en_almacen" {{ ($filters['status'] ?? '') === 'en_almacen' ? 'selected' : '' }}>En Almacen</option>
                                <option value="embarcado" {{ ($filters['status'] ?? '') === 'embarcado' ? 'selected' : '' }}>Embarcadas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Desde</label>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                                   class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-pink-500 text-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Hasta</label>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                                   class="w-full border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-sm focus:ring-pink-500 text-gray-900 dark:text-gray-100">
                        </div>
                    </div>
                </form>
            </div>

            {{-- TABLA --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                @if($reportData->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Caja</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Articulo</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Contenedor</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tarima</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Paso Actual</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tiempo Total</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Detalle</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                @foreach($reportData as $row)
                                    @php
                                        $stepConfig = match($row->paso_actual) {
                                            'en_recepcion'              => ['bg' => 'bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-200',           'icon' => 'fa-inbox',     'label' => 'En Recepcion'],
                                            'disponible_sin_tarima'     => ['bg' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300', 'icon' => 'fa-box-open',  'label' => 'Sin Tarima'],
                                            'en_tarima_abierta'         => ['bg' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',         'icon' => 'fa-pallet',    'label' => 'Tarima Abierta'],
                                            'tarima_cerrada_sin_ubicar' => ['bg' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300', 'icon' => 'fa-lock',      'label' => 'Sin Ubicar'],
                                            'en_rack'                   => ['bg' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300', 'icon' => 'fa-warehouse', 'label' => 'En Rack'],
                                            'en_maquila'                => ['bg' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300', 'icon' => 'fa-cogs',      'label' => 'En Maquila'],
                                            'embarcada'                 => ['bg' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',     'icon' => 'fa-truck',     'label' => 'Embarcada'],
                                            default                     => ['bg' => 'bg-gray-100 text-gray-600', 'icon' => 'fa-question', 'label' => $row->paso_actual],
                                        };

                                        $diasTotal = $row->duraciones['total_almacen_dias'] ?? 0;
                                        $tiempoColor = match(true) {
                                            $diasTotal > 30 => 'text-red-600 dark:text-red-400 font-bold',
                                            $diasTotal > 14 => 'text-amber-600 dark:text-amber-400 font-semibold',
                                            $diasTotal > 7  => 'text-yellow-600 dark:text-yellow-400',
                                            default         => 'text-green-600 dark:text-green-400',
                                        };
                                    @endphp
                                    
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition cursor-pointer"
                                        x-data="{ open: false }" @click="open = !open">
                                        
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white font-mono">{{ $row->caja_codigo }}</span>
                                            <br>
                                            <span class="text-[10px] uppercase font-bold {{ $row->source === 'contenedor' ? 'text-teal-500' : 'text-indigo-500' }}">
                                                {{ $row->source === 'contenedor' ? 'Original' : 'Reempaque' }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-3">
                                            <span class="text-xs font-mono font-bold text-gray-700 dark:text-gray-300">{{ $row->sku }}</span>
                                            <br>
                                            <span class="text-xs text-gray-500 truncate block max-w-[200px]" title="{{ $row->articulo }}">{{ $row->articulo }}</span>
                                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-medium">{{ $row->cantidad }} pzas</span>
                                        </td>

                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $row->contenedor_sello }}</span>
                                        </td>

                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <span class="text-xs font-mono font-medium {{ $row->tarima_codigo === "\xe2\x80\x94" ? 'text-red-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $row->tarima_codigo }}</span>
                                            @if($row->localidad !== "\xe2\x80\x94")
                                                <p class="text-[10px] text-teal-600 dark:text-teal-400 font-medium mt-0.5">
                                                    <i class="fas fa-map-marker-alt mr-0.5"></i>{{ $row->localidad }}
                                                </p>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $stepConfig['bg'] }}">
                                                <i class="fas {{ $stepConfig['icon'] }} mr-1"></i>{{ $stepConfig['label'] }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <span class="{{ $tiempoColor }} text-sm font-mono">{{ $row->duraciones['total_almacen'] ?? "\xe2\x80\x94" }}</span>
                                            @if($diasTotal > 0)
                                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $diasTotal }} dias</p>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <button class="text-gray-400 hover:text-pink-600 transition" @click.stop="open = !open">
                                                <i class="fas fa-chevron-down transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    {{-- FILA EXPANDIBLE --}}
                                    <tr x-show="open" x-cloak class="bg-gray-50/50 dark:bg-gray-900/30">
                                        <td colspan="7" class="px-4 py-5">
                                            <div class="max-w-4xl mx-auto space-y-5">
                                                
                                                {{-- Timeline Visual --}}
                                                <div class="flex items-start justify-between px-2 overflow-x-auto">
                                                    @php
                                                        $steps = [
                                                            ['key' => 'contenedor_recibido', 'icon' => 'fa-ship',      'label' => 'Recibido',    'c' => 'teal'],
                                                            ['key' => 'caja_creada',         'icon' => 'fa-box',       'label' => 'Caja Creada', 'c' => 'blue'],
                                                            ['key' => 'asignada_a_tarima',   'icon' => 'fa-pallet',    'label' => 'En Tarima',   'c' => 'indigo'],
                                                            ['key' => 'tarima_cerrada',      'icon' => 'fa-lock',      'label' => 'Cerrada',     'c' => 'orange'],
                                                            ['key' => 'tarima_ubicada',      'icon' => 'fa-warehouse', 'label' => 'En Rack',     'c' => 'purple'],
                                                            ['key' => 'embarcada',           'icon' => 'fa-truck',     'label' => 'Embarcada',   'c' => 'green'],
                                                        ];
                                                    @endphp

                                                    @foreach($steps as $index => $step)
                                                        @php
                                                            $ts = $row->hitos[$step['key']] ?? null;
                                                            $done = $ts !== null;
                                                            $cc = $step['c'];
                                                        @endphp
                                                        
                                                        <div class="flex flex-col items-center text-center flex-shrink-0" style="min-width: 75px;">
                                                            <div class="w-9 h-9 rounded-full border-2 flex items-center justify-center {{ $done ? "bg-{$cc}-100 dark:bg-{$cc}-900/30 border-{$cc}-400 dark:border-{$cc}-600" : 'bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600' }}">
                                                                <i class="fas {{ $step['icon'] }} text-xs {{ $done ? "text-{$cc}-600 dark:text-{$cc}-400" : 'text-gray-400' }}"></i>
                                                            </div>
                                                            <span class="text-[10px] font-bold mt-1.5 {{ $done ? "text-{$cc}-600 dark:text-{$cc}-400" : 'text-gray-400' }}">{{ $step['label'] }}</span>
                                                            <span class="text-[9px] text-gray-400 mt-0.5">{{ $ts ? $ts->format('d/m/y H:i') : "\xe2\x80\x94" }}</span>
                                                        </div>

                                                        @if($index < count($steps) - 1)
                                                            @php
                                                                $nextTs = $row->hitos[$steps[$index + 1]['key']] ?? null;
                                                                $lineActive = $done && $nextTs;
                                                            @endphp
                                                            <div class="flex-1 h-0.5 mt-4 mx-1 rounded {{ $lineActive ? "bg-{$cc}-400" : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                                                        @endif
                                                    @endforeach
                                                </div>

                                                {{-- Duraciones --}}
                                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                                                    @php
                                                        $durs = [
                                                            ['key' => 'en_recepcion', 'label' => 'Recepcion',     'icon' => 'fa-inbox',     'c' => 'teal'],
                                                            ['key' => 'sin_tarima',   'label' => 'Sin Tarima',    'icon' => 'fa-box-open',  'c' => 'yellow'],
                                                            ['key' => 'en_armado',    'label' => 'Armado Tarima', 'icon' => 'fa-pallet',    'c' => 'blue'],
                                                            ['key' => 'en_rack',      'label' => 'Almacenaje',    'icon' => 'fa-warehouse', 'c' => 'purple'],
                                                            ['key' => 'en_maquila',   'label' => 'Maquila',       'icon' => 'fa-cogs',      'c' => 'orange'],
                                                        ];
                                                    @endphp
                                                    
                                                    @foreach($durs as $dur)
                                                        @php $val = $row->duraciones[$dur['key']] ?? null; @endphp
                                                        @if($val)
                                                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 text-center">
                                                                <i class="fas {{ $dur['icon'] }} text-{{ $dur['c'] }}-500 text-sm mb-1"></i>
                                                                <p class="text-sm font-bold font-mono text-gray-800 dark:text-white">{{ $val }}</p>
                                                                <p class="text-[10px] text-gray-500 uppercase font-semibold mt-0.5">{{ $dur['label'] }}</p>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>

                                                {{-- Info adicional --}}
                                                <div class="flex flex-wrap items-center gap-4 pt-2 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-500">
                                                    @if($row->despachado_por)
                                                        <span><i class="fas fa-user mr-1"></i> Despachado por: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $row->despachado_por }}</span></span>
                                                    @endif
                                                    @if($row->hitos['maquila_inicio'] ?? null)
                                                        <span class="text-purple-600 dark:text-purple-400">
                                                            <i class="fas fa-cogs mr-1"></i> Maquila: {{ $row->hitos['maquila_inicio']->format('d/m/y H:i') }}
                                                            @if($row->hitos['maquila_completa'] ?? null)
                                                                &rarr; {{ $row->hitos['maquila_completa']->format('d/m/y H:i') }}
                                                            @else
                                                                <span class="text-amber-500 font-medium ml-1">(en proceso)</span>
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400">
                        Mostrando {{ $reportData->count() }} {{ $reportData->count() === 1 ? 'caja' : 'cajas' }}
                        @if(!empty(array_filter($filters ?? [])))
                            <span class="text-pink-500 font-medium">(filtros activos)</span>
                        @endif
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-full mb-4">
                                <i class="fas fa-route text-3xl text-gray-300 dark:text-gray-500"></i>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 font-medium text-lg">No se encontraron cajas</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                                @if(!empty(array_filter($filters ?? [])))
                                    Intenta ajustar los filtros o
                                    <a href="{{ route('reports.storage-time') }}" class="text-pink-500 hover:underline">limpiar la busqueda</a>
                                @else
                                    Las cajas apareceran aqui conforme se registren contenedores y se creen cajas
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- LEYENDA --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-3">
                    <i class="fas fa-info-circle mr-1"></i> Leyenda del Ciclo de Vida
                </p>
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-200"><i class="fas fa-inbox mr-1"></i> En Recepcion</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300"><i class="fas fa-box-open mr-1"></i> Sin Tarima</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"><i class="fas fa-pallet mr-1"></i> Tarima Abierta</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300"><i class="fas fa-lock mr-1"></i> Sin Ubicar</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300"><i class="fas fa-warehouse mr-1"></i> En Rack</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300"><i class="fas fa-cogs mr-1"></i> En Maquila</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300"><i class="fas fa-truck mr-1"></i> Embarcada</span>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>