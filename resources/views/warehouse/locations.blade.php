<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 p-3 rounded-lg shadow-lg"><i class="fas fa-warehouse text-white text-xl"></i></div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Localidades del Almacén</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Asignación y movimiento de tarimas</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showNewLocation: false, showAssign: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))<div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif
            @if(session('error'))<div class="p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>@endif

            {{-- Barra de acciones --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <form method="GET" class="flex-1 flex items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar localidad..." class="flex-1 max-w-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500">
                    @if($zones->count())
                        <select name="zone" class="border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100">
                            <option value="">Todas las zonas</option>
                            @foreach($zones as $z)<option value="{{ $z }}" {{ request('zone') === $z ? 'selected' : '' }}>{{ $z }}</option>@endforeach
                        </select>
                    @endif
                    <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-lg text-sm"><i class="fas fa-filter mr-1"></i> Filtrar</button>
                </form>
                <div class="flex gap-2">
                    <button @click="showNewLocation = !showNewLocation" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-500 text-sm"><i class="fas fa-plus mr-1"></i> Nueva Localidad</button>
                    @if($unassignedPallets->count())
                        <button @click="showAssign = !showAssign" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-500 text-sm"><i class="fas fa-link mr-1"></i> Asignar Tarima ({{ $unassignedPallets->count() }})</button>
                    @endif
                    <a href="{{ route('warehouse.transfers') }}" class="px-4 py-2 bg-slate-600 text-white rounded-lg text-sm"><i class="fas fa-history mr-1"></i> Historial</a>
                </div>
            </div>

            {{-- Formulario nueva localidad --}}
            <div x-show="showNewLocation" x-collapse x-cloak class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4"><i class="fas fa-map-marker-alt text-emerald-500 mr-2"></i>Nueva Localidad</h3>
                <form method="POST" action="{{ route('warehouse.store-location') }}" class="grid grid-cols-2 md:grid-cols-7 gap-3 items-end">
                    @csrf
                    <div><label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Código *</label><input type="text" name="code" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100" placeholder="A-01-1"></div>
                    <div><label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Nombre *</label><input type="text" name="name" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100" placeholder="Rack A Nivel 1"></div>
                    <div><label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Zona</label><input type="text" name="zone" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100" placeholder="A"></div>
                    <div><label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Pasillo</label><input type="text" name="aisle" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100" placeholder="01"></div>
                    <div><label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Nivel</label><input type="text" name="level" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100" placeholder="1"></div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Tipo</label>
                        <select name="type" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100">
                            <option value="rack">Rack</option><option value="piso">Piso</option><option value="andén">Andén</option><option value="tránsito">Tránsito</option><option value="otro">Otro</option>
                        </select>
                    </div>
                    <div><button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm"><i class="fas fa-save mr-1"></i> Crear</button></div>
                </form>
            </div>

            {{-- Asignar tarima sin localidad --}}
            <div x-show="showAssign" x-collapse x-cloak class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4"><i class="fas fa-link text-purple-500 mr-2"></i>Asignar Tarima a Localidad</h3>
                <form method="POST" action="{{ route('warehouse.assign-pallet') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Tarima</label>
                        <select name="pallet_id" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100">
                            <option value="">Seleccionar...</option>
                            @foreach($unassignedPallets as $p)
                                <option value="{{ $p->id }}">{{ $p->pallet_code }} — {{ $p->container->container_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Localidad destino</label>
                        <select name="location_id" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2.5 px-3 text-gray-900 dark:text-gray-100">
                            <option value="">Seleccionar...</option>
                            @foreach($locations as $loc)<option value="{{ $loc->id }}">{{ $loc->code }} — {{ $loc->name }}</option>@endforeach
                        </select>
                    </div>
                    <div><button type="submit" class="w-full px-4 py-2.5 bg-purple-600 text-white rounded-lg text-sm"><i class="fas fa-check mr-1"></i> Asignar</button></div>
                </form>
            </div>

            {{-- Grid de localidades --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($locations as $loc)
                    <a href="{{ route('warehouse.show-location', $loc) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 hover:shadow-lg transition border-l-4 {{ $loc->pallets_count > 0 ? 'border-emerald-500' : 'border-gray-300 dark:border-gray-600' }}">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-gray-900 dark:text-white">{{ $loc->code }}</h4>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ $loc->type }}</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $loc->name }}</p>
                        @if($loc->zone)<p class="text-xs text-gray-400 mt-1">Zona {{ $loc->zone }} {{ $loc->aisle ? '· Pasillo ' . $loc->aisle : '' }}</p>@endif
                        <div class="mt-3 flex items-center space-x-4 text-sm">
                            <span class="font-bold {{ $loc->pallets_count > 0 ? 'text-emerald-600' : 'text-gray-400' }}"><i class="fas fa-pallet mr-1"></i>{{ $loc->pallets_count }} tarimas</span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
                        <i class="fas fa-warehouse text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 font-medium">No hay localidades registradas</p>
                    </div>
                @endforelse
            </div>

            @if($locations->hasPages())<div class="mt-4">{{ $locations->links() }}</div>@endif
        </div>
    </div>
</x-app-layout>
