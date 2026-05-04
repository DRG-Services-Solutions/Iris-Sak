<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-pink-600 to-rose-700 p-3 rounded-lg shadow-lg"><i class="fas fa-cogs text-white text-xl"></i></div>
                <div><h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Maquila</h2><p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Control de estaciones de trabajo</p></div>
            </div>
            <a href="{{ route('maquila.logs') }}" class="hidden md:inline-flex px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 text-sm"><i class="fas fa-history mr-1"></i> Historial</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))<div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif

            {{-- KPIs --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center border-b-4 border-gray-300"><p class="text-2xl font-bold text-gray-600">{{ $stationCounts['sin_iniciar'] }}</p><p class="text-xs text-gray-500">Sin iniciar</p></div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center border-b-4 border-amber-400"><p class="text-2xl font-bold text-amber-600">{{ $stationCounts[1] }}</p><p class="text-xs text-gray-500">Estación 1</p></div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center border-b-4 border-blue-400"><p class="text-2xl font-bold text-blue-600">{{ $stationCounts[2] }}</p><p class="text-xs text-gray-500">Estación 2</p></div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center border-b-4 border-purple-400"><p class="text-2xl font-bold text-purple-600">{{ $stationCounts[3] }}</p><p class="text-xs text-gray-500">Estación 3</p></div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center border-b-4 border-green-400"><p class="text-2xl font-bold text-green-600">{{ $stationCounts['completado'] }}</p><p class="text-xs text-gray-500">Completado</p></div>
            </div>

            <div class="md:hidden"><a href="{{ route('maquila.logs') }}" class="flex items-center justify-center w-full px-4 py-2.5 bg-slate-700 text-white rounded-lg text-sm"><i class="fas fa-history mr-2"></i> Historial</a></div>

            {{-- Ubicaciones --}}
            @forelse($locations as $location)
                @php
                    $palletsInLocation = $location->pallets;
                    if ($stationFilter === 'sin_iniciar') { $palletsInLocation = $palletsInLocation->filter(fn($p) => $p->maquila_started_at === null); }
                    elseif ($stationFilter === 'completado') { $palletsInLocation = $palletsInLocation->filter(fn($p) => $p->maquila_completed_at !== null); }
                    elseif (is_numeric($stationFilter)) { $palletsInLocation = $palletsInLocation->filter(fn($p) => $p->maquila_station == $stationFilter && $p->maquila_completed_at === null); }
                    if ($palletsInLocation->isEmpty() && $stationFilter) { continue; }
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden" x-data="{ open: true }">
                    <div class="px-5 py-4 flex items-center justify-between cursor-pointer border-b border-gray-200 dark:border-gray-700" @click="open = !open">
                        <div class="flex items-center space-x-3">
                            <div class="bg-emerald-100 dark:bg-emerald-900/30 p-2 rounded-lg"><i class="fas fa-map-marker-alt text-emerald-600"></i></div>
                            <div><h3 class="font-bold text-gray-900 dark:text-white">{{ $location->code }}</h3><p class="text-xs text-gray-500">{{ $location->name }} · {{ $palletsInLocation->count() }} tarimas</p></div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @php
                                $locE1 = $location->pallets->filter(fn($p) => $p->maquila_station === 1 && !$p->maquila_completed_at)->count();
                                $locE2 = $location->pallets->filter(fn($p) => $p->maquila_station === 2 && !$p->maquila_completed_at)->count();
                                $locE3 = $location->pallets->filter(fn($p) => $p->maquila_station === 3 && !$p->maquila_completed_at)->count();
                                $locDone = $location->pallets->filter(fn($p) => $p->maquila_completed_at !== null)->count();
                            @endphp
                            @if($locE1)<span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">E1: {{ $locE1 }}</span>@endif
                            @if($locE2)<span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">E2: {{ $locE2 }}</span>@endif
                            @if($locE3)<span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">E3: {{ $locE3 }}</span>@endif
                            @if($locDone)<span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-bold"><i class="fas fa-check mr-0.5"></i>{{ $locDone }}</span>@endif
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    <div x-show="open" x-collapse>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($palletsInLocation->sortBy('pallet_code') as $pallet)
                                @php
                                    $stationBadge = match($pallet->maquila_station) { 1 => 'bg-amber-100 text-amber-800 border-amber-300', 2 => 'bg-blue-100 text-blue-800 border-blue-300', 3 => 'bg-purple-100 text-purple-800 border-purple-300', default => 'bg-gray-100 text-gray-600 border-gray-300' };
                                    $isCompleted = $pallet->maquila_completed_at !== null;
                                @endphp
                                <div class="px-5 py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-3 {{ $isCompleted ? 'bg-green-50/50 dark:bg-green-900/5' : '' }}">
                                    <div class="flex items-center space-x-3 flex-1">
                                        <div>
                                            <p class="font-mono font-bold text-sm text-gray-900 dark:text-white">{{ $pallet->pallet_code }}</p>
                                            <p class="text-xs text-gray-500">{{ $pallet->container->container_number }} · {{ $pallet->boxes->count() }} cajas · {{ number_format($pallet->boxes->sum('quantity')) }} pzas</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($isCompleted)
                                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 border border-green-300 rounded-full text-xs font-bold"><i class="fas fa-check-circle mr-1"></i> Completado</span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 {{ $stationBadge }} border rounded-full text-xs font-bold">{{ $pallet->maquila_status_label }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2 flex-shrink-0">
                                        @if(!$isCompleted)
                                            <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-0.5">
                                                @foreach([1, 2, 3] as $st)
                                                    <form method="POST" action="{{ route('maquila.move', $pallet) }}" class="inline">@csrf<input type="hidden" name="station" value="{{ $st }}">
                                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold rounded-md transition {{ $pallet->maquila_station === $st ? match($st) { 1 => 'bg-amber-500 text-white shadow', 2 => 'bg-blue-500 text-white shadow', 3 => 'bg-purple-500 text-white shadow' } : 'text-gray-500 hover:bg-white dark:hover:bg-gray-600 hover:shadow' }}" {{ $pallet->maquila_station === $st ? 'disabled' : '' }}>E{{ $st }}</button>
                                                    </form>
                                                @endforeach
                                            </div>
                                            @if($pallet->maquila_station !== null)
                                                <form method="POST" action="{{ route('maquila.complete', $pallet) }}" class="inline">@csrf<button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-medium hover:bg-green-500 transition"><i class="fas fa-check mr-1"></i> Completar</button></form>
                                            @endif
                                        @else
                                            <form method="POST" action="{{ route('maquila.move', $pallet) }}" class="inline-flex items-center space-x-1">@csrf
                                                <select name="station" class="border border-gray-300 dark:border-gray-600 rounded text-xs py-1 px-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"><option value="1">E1</option><option value="2">E2</option><option value="3">E3</option></select>
                                                <button type="submit" class="px-2 py-1 bg-amber-500 text-white rounded text-xs" title="Reabrir"><i class="fas fa-undo"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center"><i class="fas fa-cogs text-4xl text-gray-300 mb-3"></i><p class="text-gray-500 font-medium">No hay ubicaciones con tarimas</p><p class="text-sm text-gray-400 mt-1">Asigne tarimas a localidades desde el módulo de almacén</p></div>
            @endforelse
        </div>
    </div>
</x-app-layout>
