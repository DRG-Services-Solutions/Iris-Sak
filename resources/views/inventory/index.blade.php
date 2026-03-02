<x-app-layout>
    {{-- Encabezado --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-blue-700 to-slate-900 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-warehouse text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        Inventario
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Existencias y control de stock</p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <div class="text-center px-4 py-2 bg-slate-700 rounded-lg">
                    <p class="text-2xl font-bold text-white">{{ $uniqueProducts }}</p>
                    <p class="text-xs text-gray-300">Total Productos</p>
                 
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Mensajes de sesión --}}
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <p class="text-green-700 dark:text-green-300 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <p class="text-red-700 dark:text-red-300 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Estadísticas Rápidas --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                {{-- Total Productos --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Productos</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $uniqueProducts }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-boxes text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Productos RFID --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Productos RFID</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $products->where('tracking_type', 'rfid')->sum('instances_count') }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-broadcast-tower text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Productos Código de Barras --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Código de Barras</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $barcodeProducts }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-barcode text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Stock Bajo --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Stock Bajo</p>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $lowStockProducts }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Barra de búsqueda y filtros --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <form method="GET" action="{{ route('inventory.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        
                        {{-- Búsqueda --}}
                        <div class="md:col-span-2">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                       placeholder="Buscar por nombre o código de barras...">
                            </div>
                        </div>

                        {{-- Filtro por tipo --}}
                        <select name="type" class="border p-2 rounded">
                            <option value="">Todos los tipos</option>
                            <option value="rfid" {{ request('type') == 'rfid' ? 'selected' : '' }}>RFID</option>
                            <option value="barcode" {{ request('type') == 'barcode' ? 'selected' : '' }}>Barcode</option>
                        </select>

                        {{-- Filtro por stock --}}
                        <div>
                            <select name="stock_filter" 
                                    class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Todo el stock</option>
                                <option value="available" {{ request('stock_filter') === 'available' ? 'selected' : '' }}>Con stock</option>
                                <option value="low" {{ request('stock_filter') === 'low' ? 'selected' : '' }}>Stock bajo (&lt;20)</option>
                                <option value="out" {{ request('stock_filter') === 'out' ? 'selected' : '' }}>Sin stock</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition-colors">
                            <i class="fas fa-filter mr-2"></i>
                            Aplicar Filtros
                        </button>

                        @if(request()->hasAny(['search', 'type', 'stock_filter']))
                            <a href="{{ route('inventory.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold text-sm rounded-lg transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Limpiar Filtros
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla de Inventario --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gradient-to-r from-slate-700 to-slate-800">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-100 uppercase tracking-wider">
                                    <i class="fas fa-box mr-2"></i>Producto
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-100 uppercase tracking-wider">
                                    <i class="fas fa-barcode mr-2"></i>Código
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-100 uppercase tracking-wider">
                                    <i class="fas fa-tag mr-2"></i>Tipo de Rastreo
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-100 uppercase tracking-wider">
                                    <i class="fas fa-layer-group mr-2"></i>Stock
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-100 uppercase tracking-wider">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Ubicación
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-100 uppercase tracking-wider">
                                    <i class="fas fa-info-circle mr-2"></i>Estado
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-100 uppercase tracking-wider">
                                    <i class="fas fa-cog mr-2"></i>Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($products as $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    {{-- Producto --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-cube text-blue-600 dark:text-blue-300"></i>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase">
                                                    {{ $product->name }}
                                                </div>
                                                @if($product->description)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ Str::limit($product->description, 50) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Código de Barras --}}
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-barcode mr-1.5 text-gray-400"></i>
                                            {{ $product->barcode }}
                                        </span>
                                    </td>

                                    {{-- Tipo de Tracking --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($product->isRfidTracked())
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                                <i class="fas fa-broadcast-tower mr-1"></i>
                                                RFID
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                                                <i class="fas fa-barcode mr-1"></i>
                                                BARCODE
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Stock --}}
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $stock = $product->stock;
                                            $stockClass = $stock == 0 
                                                ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-800' 
                                                : ($stock < 20 
                                                    ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800' 
                                                    : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800');
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-black border {{ $stockClass }}">
                                            @if($stock == 0)
                                                <i class="fas fa-times-circle mr-1.5"></i>
                                            @elseif($stock < 10)
                                                <i class="fas fa-exclamation-triangle mr-1.5"></i>
                                            @else
                                                <i class="fas fa-check-circle mr-1.5"></i>
                                            @endif
                                            {{ $stock }}
                                        </span>
                                    </td>

                                    {{-- Ubicación --}}
                                    <td class="px-6 py-4 text-center">
                                        <div class="text-sm text-gray-700 dark:text-gray-300">
                                            @if($product->current_station)
                                                <span class="inline-flex items-center">
                                                    <i class="fas fa-location-dot text-gray-400 mr-1.5"></i>
                                                    {{ $product->current_station }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                        @if($product->branch)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                <i class="fas fa-building text-gray-400 mr-1"></i>
                                                {{ $product->branch->name }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Estado --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($product->status === 'available')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                                Disponible
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                <span class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1.5"></span>
                                                {{ ucfirst($product->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('products.show', $product) }}" 
                                               class="inline-flex items-center justify-center w-8 h-8 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($product->isRfidTracked())
                                                <a href="{{ route('movements.show', $product) }}" 
                                                   class="inline-flex items-center justify-center w-8 h-8 text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors"
                                                   title="Ver instancias">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('movements.show', $product) }}" 
                                               class="inline-flex items-center justify-center w-8 h-8 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors"
                                               title="Ver movimientos">
                                                <i class="fas fa-history"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <i class="fas fa-inbox text-gray-300 dark:text-gray-600 text-6xl"></i>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm font-semibold">No se encontraron productos</p>
                                            <p class="text-gray-400 dark:text-gray-500 text-xs">Intenta ajustar los filtros de búsqueda</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $products->links() }}

                </div>
            </div>
        </div>
       

    </div>
</x-app-layout>