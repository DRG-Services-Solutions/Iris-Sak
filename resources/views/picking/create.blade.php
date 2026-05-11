<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('picking.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"><i class="fas fa-arrow-left text-lg"></i></a>
            <div class="bg-gradient-to-br from-orange-500 to-orange-700 p-3 rounded-lg shadow-lg"><i class="fas fa-plus text-white text-xl"></i></div>
            <div><h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Nueva Orden de Surtido</h2></div>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))<div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>@endif

            <form method="POST" action="{{ route('picking.store') }}" class="space-y-6">
                @csrf
                
                {{-- Datos de la Orden --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white"><i class="fas fa-info-circle text-orange-500 mr-2"></i>Datos de la Orden</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Cliente *</label><input type="text" name="client_name" required value="{{ old('client_name') }}" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100" placeholder="Nombre del cliente"></div>
                        <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Destino *</label><input type="text" name="destination" required value="{{ old('destination') }}" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100" placeholder="Ciudad o dirección"></div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Prioridad</label>
                            <select name="priority" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100">
                                <option value="normal">Normal</option><option value="urgente">Urgente</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Asignar a</label>
                            <select name="assigned_to" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100">
                                <option value="">Sin asignar</option>
                                @foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Notas</label><textarea name="notes" rows="2" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100" placeholder="Observaciones...">{{ old('notes') }}</textarea></div>
                </div>

                {{-- Selección de Tarimas por Contenedor --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white"><i class="fas fa-pallet text-orange-500 mr-2"></i>Seleccionar Tarimas a Surtir</h3>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $availablePallets->count() }} tarimas disponibles</span>
                    </div>

                    @if($availablePallets->count())
                        @php
                            // Agrupamos las tarimas por contenedor
                            $groupedPallets = $availablePallets->groupBy(function($pallet) {
                                return $pallet->container ? $pallet->container->container_seal_number : 'Sin Contenedor Asignado';
                            });
                        @endphp

                        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
                            @foreach($groupedPallets as $containerNumber => $pallets)
                                {{-- Contenedor Acordeón --}}
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden" x-data="{ expanded: true }">
                                    {{-- Cabecera del Contenedor --}}
                                    <div @click="expanded = !expanded" class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 flex items-center justify-between cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-teal-100 dark:bg-teal-900/30 p-2 rounded text-teal-600 dark:text-teal-400">
                                                <i class="fas fa-ship"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-800 dark:text-white text-sm">Contenedor: {{ $containerNumber }}</h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $pallets->count() }} tarimas disponibles</p>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" :class="{'rotate-180': expanded}"></i>
                                    </div>

                                    {{-- Lista de Tarimas (Grid) --}}
                                    <div x-show="expanded" x-collapse x-cloak class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($pallets as $p)
                                                <label class="flex items-start p-3 bg-white dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:border-orange-400 dark:hover:border-orange-500 shadow-sm transition">
                                                    <input type="checkbox" name="pallet_ids[]" value="{{ $p->id }}" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 mt-1.5 mr-3 w-4 h-4">
                                                    <div class="text-sm flex-1">
                                                        <div class="flex justify-between items-start">
                                                            <p class="font-mono font-bold text-gray-900 dark:text-white">{{ $p->pallet_code }}</p>
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300">
                                                                <i class="fas fa-map-marker-alt mr-1 text-emerald-500"></i>{{ $p->location?->code ?? 'Sin loc.' }}
                                                            </span>
                                                        </div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            {{ $p->boxes->count() }} cajas · <span class="font-semibold">{{ number_format($p->boxes->sum('quantity')) }} pzas</span>
                                                        </p>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center bg-gray-50 dark:bg-gray-700/30 rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-600">
                            <i class="fas fa-pallet text-4xl text-gray-300 dark:text-gray-500 mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">No hay tarimas disponibles para surtir.</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Deben estar cerradas y con una localidad asignada en el almacén.</p>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('picking.index') }}" class="px-5 py-2.5 text-gray-600 dark:text-gray-300 font-medium text-sm hover:text-gray-800 transition">Cancelar</a>
                    <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-500 text-sm font-medium shadow-lg transition" {{ $availablePallets->count() ? '' : 'disabled' }}>
                        <i class="fas fa-save mr-1"></i> Crear Orden
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>