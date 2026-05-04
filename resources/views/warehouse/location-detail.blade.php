<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('warehouse.locations') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"><i class="fas fa-arrow-left text-lg"></i></a>
            <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 p-3 rounded-lg shadow-lg"><i class="fas fa-map-marker-alt text-white text-xl"></i></div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">{{ $location->code }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $location->name }} {{ $location->zone ? '· Zona ' . $location->zone : '' }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))<div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600">{{ $location->pallets->count() }}</p>
                    <p class="text-xs text-gray-500">Tarimas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-indigo-600">{{ $location->pallets->sum(fn($p) => $p->boxes->count()) }}</p>
                    <p class="text-xs text-gray-500">Cajas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-teal-600">{{ number_format($location->pallets->sum(fn($p) => $p->boxes->sum('quantity'))) }}</p>
                    <p class="text-xs text-gray-500">Piezas</p>
                </div>
            </div>

            {{-- Tarimas en esta localidad --}}
            @forelse($location->pallets as $pallet)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-3">
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white">{{ $pallet->pallet_code }}</h4>
                            <p class="text-xs text-gray-500">{{ $pallet->container->container_number }} · {{ $pallet->boxes->count() }} cajas · {{ $pallet->boxes->sum('quantity') }} pzas</p>
                        </div>
                        <form method="POST" action="{{ route('warehouse.transfer-pallet', $pallet) }}" class="flex items-center space-x-2">
                            @csrf
                            <select name="to_location_id" required class="border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100">
                                <option value="">Transferir a...</option>
                                @foreach($allLocations as $dest)<option value="{{ $dest->id }}">{{ $dest->code }} — {{ $dest->name }}</option>@endforeach
                            </select>
                            <button type="submit" class="px-3 py-2 bg-amber-600 text-white rounded-lg text-sm"><i class="fas fa-exchange-alt mr-1"></i> Mover</button>
                        </form>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($pallet->boxes->sortBy('box_code') as $box)
                            <span class="inline-flex items-center px-2 py-1 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-xs font-mono">{{ $box->containerItem->barcode }}-{{ $box->containerItem->product_description }} <span class="text-gray-400 ml-1">({{ $box->quantity }})</span></span>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 font-medium">Localidad vacía</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
