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
                    <p class="text-2xl font-bold text-amber-600">
                        {{ $stats['promedio_dias'] >= 1 ? $stats['promedio_dias'] . ' d' : round($stats['promedio_dias'] * 24, 1) . ' h' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Promedio Tiempo</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">
                        {{ $stats['max_dias'] >= 1 ? $stats['max_dias'] . ' d' : round($stats['max_dias'] * 24, 1) . ' h' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Max. Tiempo</p>
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

                            <a target="_blank" href="{{ route('reports.storage-time.pdf', request()->all()) }}" class="px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-500 transition font-medium text-sm flex items-center shadow">
                                <i class="fas fa-file-pdf mr-2"></i> PDF
                            </a>
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

            {{-- REPORTE AGRUPADO (Contenedor -> Tarima -> Caja) --}}
            <div class="space-y-6">
                @if($reportData->count())
                    @php
                        // Agrupar 1er Nivel: Por Contenedor
                        $groupedByContainer = $reportData->groupBy('contenedor_sello');
                    @endphp

                    <div class="space-y-4">
                        @foreach($groupedByContainer as $contenedorSello => $boxesInContainer)
                            @php
                                $maxDiasCont = $boxesInContainer->max(fn($b) => $b->duraciones['total_almacen_dias'] ?? 0);
                                $labelCont = $maxDiasCont >= 1 ? round($maxDiasCont, 2) . ' días' : round($maxDiasCont * 24, 2) . ' horas';
                                
                                $colorCont = match(true) {
                                    $maxDiasCont > 30 => 'text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30',
                                    $maxDiasCont > 14 => 'text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/30',
                                    $maxDiasCont > 7  => 'text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/30',
                                    default           => 'text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30',
                                };
                                $containerLabel = trim($contenedorSello, " \t\n\r\0\x0B\xE2\x80\x94") === '' ? 'Sin Contenedor Asignado' : $contenedorSello;
                            @endphp

                            {{-- NIVEL 1: CONTENEDOR --}}
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700" x-data="{ openCont: true }">
                                <div @click="openCont = !openCont" class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex items-center justify-between cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-pink-100 dark:bg-pink-900/30 p-3 rounded-lg text-pink-600 dark:text-pink-400">
                                            <i class="fas fa-ship text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">{{ $containerLabel }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $boxesInContainer->count() }} cajas totales</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-6">
                                        <div class="text-right hidden md:block">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold tracking-wider mb-1">Max Lead Time</p>
                                            <span class="px-3 py-1 rounded-full text-sm font-bold {{ $colorCont }}">
                                                <i class="fas fa-clock mr-1"></i> {{ $labelCont }}
                                            </span>
                                        </div>
                                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" :class="{'rotate-180': openCont}"></i>
                                    </div>
                                </div>

                                <div x-show="openCont" x-collapse x-cloak class="p-4 space-y-4">
                                    @php
                                        // Agrupar 2do Nivel: Por Tarima
                                        $groupedByPallet = $boxesInContainer->groupBy('tarima_codigo');
                                    @endphp

                                    @foreach($groupedByPallet as $tarimaCodigo => $boxesInPallet)
                                        @php
                                            $maxDiasPallet = $boxesInPallet->max(fn($b) => $b->duraciones['total_almacen_dias'] ?? 0);
                                            $labelPallet = $maxDiasPallet >= 1 ? round($maxDiasPallet, 2) . ' días' : round($maxDiasPallet * 24, 2) . ' horas';
                                            
                                            $colorPallet = match(true) {
                                                $maxDiasPallet > 30 => 'text-red-600',
                                                $maxDiasPallet > 14 => 'text-amber-600',
                                                $maxDiasPallet > 7  => 'text-yellow-600',
                                                default             => 'text-green-600',
                                            };
                                            $isUnassigned = trim($tarimaCodigo, " \t\n\r\0\x0B\xE2\x80\x94") === '';
                                            $palletLabel = $isUnassigned ? 'Cajas Sueltas / Sin Tarima' : $tarimaCodigo;
                                        @endphp

                                        {{-- NIVEL 2: TARIMA --}}
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden" x-data="{ openPallet: false }">
                                            <div @click="openPallet = !openPallet" class="px-4 py-3 bg-white dark:bg-gray-800 flex items-center justify-between cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition border-b border-transparent" :class="{'border-gray-200 dark:border-gray-700': openPallet}">
                                                <div class="flex items-center space-x-3">
                                                    <i class="fas fa-pallet {{ $isUnassigned ? 'text-gray-400' : 'text-blue-500' }}"></i>
                                                    <span class="font-mono font-bold text-gray-800 dark:text-gray-200">{{ $palletLabel }}</span>
                                                    <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ $boxesInPallet->count() }} cajas</span>
                                                </div>
                                                <div class="flex items-center space-x-4">
                                                    <span class="text-sm font-bold {{ $colorPallet }}"><i class="fas fa-stopwatch mr-1"></i> {{ $labelPallet }} en almacén</span>
                                                    <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200 text-sm" :class="{'rotate-180': openPallet}"></i>
                                                </div>
                                            </div>

                                            {{-- NIVEL 3: CAJAS (Tabla) --}}
                                            <div x-show="openPallet" x-collapse x-cloak class="overflow-x-auto bg-gray-50/30 dark:bg-gray-900/10">
                                                <table class="min-w-full">
                                                    <thead class="bg-gray-100/50 dark:bg-gray-700/30 border-b border-gray-200 dark:border-gray-700">
                                                        <tr>
                                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Caja</th>
                                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Artículo</th>
                                                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Paso Actual</th>
                                                            <th class="px-4 py-2 text-center text-xs font-bold text-pink-600 dark:text-pink-400 uppercase">Lead Time Total</th>
                                                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Línea de Vida</th>
                                                        </tr>
                                                    </thead>
                                                    
                                                    {{-- EL TRUCO: Un tbody independiente por cada par de filas --}}
                                                    @foreach($boxesInPallet as $row)
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

                                                            $diasTotal = $row
                                                            ->duraciones['total_almacen_dias'] ?? 0;
                                                            $tiempoColor = match(true) {
                                                                $diasTotal > 30 => 'text-red-600 dark:text-red-400 font-bold',
                                                                $diasTotal > 14 => 'text-amber-600 dark:text-amber-400 font-bold',
                                                                $diasTotal > 7  => 'text-yellow-600 dark:text-yellow-400 font-semibold',
                                                                default         => 'text-green-600 dark:text-green-400 font-medium',
                                                            };
                                                            $labelCaja = $diasTotal >= 1 ? round($diasTotal, 2) . ' días netos' : round($diasTotal * 24, 2) . ' horas netas';
                                                        @endphp

                                                        <tbody x-data="{ openBox: false }" class="border-b border-gray-200 dark:border-gray-700/50">
                                                            
                                                            {{-- FILA PRINCIPAL --}}
                                                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700/50 transition cursor-pointer" @click="openBox = !openBox">
                                                                <td class="px-4 py-3 whitespace-nowrap">
                                                                    <span class="text-xs font-bold text-gray-900 dark:text-white font-mono">{{ $row->caja_codigo }}</span><br>
                                                                    <span class="text-[9px] uppercase font-bold {{ $row->source === 'contenedor' ? 'text-teal-500' : 'text-indigo-500' }}">{{ $row->source === 'contenedor' ? 'Original' : 'Reempaque' }}</span>
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <span class="text-xs font-mono font-bold text-gray-700 dark:text-gray-300">{{ $row->sku }}</span><br>
                                                                    <span class="text-[10px] text-gray-500 truncate block max-w-[200px]" title="{{ $row->articulo }}">{{ $row->articulo }}</span>
                                                                </td>
                                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $stepConfig['bg'] }}">
                                                                        <i class="fas {{ $stepConfig['icon'] }} mr-1"></i>{{ $stepConfig['label'] }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-4 py-3 whitespace-nowrap text-center bg-pink-50/30 dark:bg-pink-900/5">
                                                                    <span class="{{ $tiempoColor }} text-sm font-mono block">{{ $row->duraciones['total_almacen'] ?? "\xe2\x80\x94" }}</span>
                                                                    @if($diasTotal > 0)
                                                                        <span class="text-[10px] text-gray-400">{{ $labelCaja }}</span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                                    <button class="text-gray-400 hover:text-pink-600 transition">
                                                                        <i class="fas fa-chevron-down transition-transform duration-200" :class="{'rotate-180': openBox}"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>

                                                            {{-- FILA EXPANDIBLE: TIMELINE DE LA CAJA --}}
                                                            <tr x-show="openBox" x-cloak class="bg-gray-100/50 dark:bg-gray-900/50">
                                                                <td colspan="5" class="px-4 py-4">
                                                                    <div class="max-w-4xl mx-auto space-y-4">
                                                                        
                                                                        {{-- Timeline Visual --}}
                                                                        <div class="flex items-start justify-between px-2 overflow-x-auto">
                                                                            @php
                                                                                $steps = [
                                                                                    ['key' => 'contenedor_recibido', 'icon' => 'fa-ship',      'label' => 'Recibido',    'c' => 'teal'],
                                                                                    ['key' => 'caja_creada',         'icon' => 'fa-box',       'label' => 'Creada',      'c' => 'blue'],
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
                                                                                    <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center {{ $done ? "bg-{$cc}-100 dark:bg-{$cc}-900/30 border-{$cc}-400 dark:border-{$cc}-600" : 'bg-gray-200 dark:bg-gray-700 border-gray-300 dark:border-gray-600' }}">
                                                                                        <i class="fas {{ $step['icon'] }} text-[10px] {{ $done ? "text-{$cc}-600 dark:text-{$cc}-400" : 'text-gray-400' }}"></i>
                                                                                    </div>
                                                                                    <span class="text-[9px] font-bold mt-1.5 {{ $done ? "text-{$cc}-600 dark:text-{$cc}-400" : 'text-gray-400' }}">{{ $step['label'] }}</span>
                                                                                    <span class="text-[8px] text-gray-400 mt-0.5">{{ $ts ? $ts->format('d/m/y H:i') : "\xe2\x80\x94" }}</span>
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

                                                                        {{-- Tiempos desglosados --}}
                                                                        <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
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
                                                                                    <div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-2 text-center shadow-sm">
                                                                                        <p class="text-[10px] text-gray-500 uppercase font-bold mb-0.5"><i class="fas {{ $dur['icon'] }} text-{{ $dur['c'] }}-500 mr-1"></i> {{ $dur['label'] }}</p>
                                                                                        <p class="text-xs font-bold font-mono text-gray-800 dark:text-white">{{ $val }}</p>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>

                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg text-xs text-gray-500 dark:text-gray-400 text-center">
                        Reporte generado con {{ $reportData->count() }} cajas totales.
                        @if(!empty(array_filter($filters ?? [])))
                            <span class="text-pink-500 font-medium ml-1">(Filtros aplicados)</span>
                        @endif
                    </div>
                @else
                    {{-- ESTADO VACÍO --}}
                    <div class="p-12 bg-white dark:bg-gray-800 rounded-lg shadow-md text-center">
                        <div class="flex flex-col items-center">
                            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-full mb-4">
                                <i class="fas fa-route text-3xl text-gray-300 dark:text-gray-500"></i>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 font-medium text-lg">No se encontraron datos</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                                @if(!empty(array_filter($filters ?? [])))
                                    Intenta ajustar los filtros o
                                    <a href="{{ route('reports.storage-time') }}" class="text-pink-500 hover:underline">limpiar la búsqueda</a>
                                @else
                                    Los datos aparecerán conforme el inventario avance en sus estados operativos.
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

        </div>
    </div>
</x-app-layout>