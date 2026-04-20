<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('containers.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div class="bg-gradient-to-br from-teal-600 to-teal-800 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-shipping-fast text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">{{ $container->container_number }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $container->packing_list_number ?? '' }}
                        {{ $container->supplier ? '· ' . $container->supplier : '' }}
                    </p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-2">
                <a href="{{ route('containers.inspection', $container) }}" class="px-3 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-500 transition text-sm font-medium">
                    <i class="fas fa-tags mr-1"></i> Inspección
                </a>
                <a href="{{ route('containers.packing', $container) }}" class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500 transition text-sm font-medium">
                    <i class="fas fa-box mr-1"></i> Empaque
                </a>
                <a href="{{ route('containers.pallets', $container) }}" class="px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-500 transition text-sm font-medium">
                    <i class="fas fa-pallet mr-1"></i> Tarimas
                </a>
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

            {{-- KPIs principales --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($container->declared_qty) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Piezas declaradas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold {{ $container->received_qty >= $container->declared_qty ? 'text-green-600' : 'text-amber-600' }}">{{ number_format($container->received_qty) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Piezas recibidas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-slate-700 dark:text-slate-300">{{ $container->total_cartons }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Cajas / Bultos</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($container->total_cbm, 2) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">CBM</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($container->total_gross_weight_kg, 1) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Peso bruto (kg)</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-indigo-600">{{ $stats['labels_total'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Etiquetas</p>
                </div>
            </div>

            {{-- Información del contenedor --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-3 text-sm">
                    <div><span class="font-semibold text-gray-800 dark:text-gray-100">Proveedor:</span> <span class="text-gray-600 dark:text-gray-300">{{ $container->supplier ?? '—' }}</span></div>
                    <div><span class="font-semibold text-gray-800 dark:text-gray-100">Comprador:</span> <span class="text-gray-600 dark:text-gray-300">{{ $container->buyer ?? '—' }}</span></div>
                    <div><span class="font-semibold text-gray-800 dark:text-gray-100">Transporte:</span> <span class="text-gray-600 dark:text-gray-300">{{ $container->transport_mode ?? '—' }}</span></div>
                    <div><span class="font-semibold text-gray-800 dark:text-gray-100">Puerto carga:</span> <span class="text-gray-600 dark:text-gray-300">{{ $container->port_loading ?? '—' }}</span></div>
                    <div><span class="font-semibold text-gray-800 dark:text-gray-100">Puerto descarga:</span> <span class="text-gray-600 dark:text-gray-300">{{ $container->port_discharge ?? '—' }}</span></div>
                    <div><span class="font-semibold text-gray-800 dark:text-gray-100">ETD:</span> <span class="text-gray-600 dark:text-gray-300">{{ $container->etd?->format('d/m/Y') ?? '—' }}</span></div>
                    <div><span class="font-semibold text-gray-800 dark:text-gray-100">ETA:</span> <span class="text-gray-600 dark:text-gray-300">{{ $container->eta?->format('d/m/Y') ?? '—' }}</span></div>
                    <div><span class="font-semibold text-gray-800 dark:text-gray-100">Recibido por:</span> <span class="text-gray-600 dark:text-gray-300">{{ $container->receivedByUser?->name ?? '—' }}</span></div>
                    <div><span class="font-semibold text-gray-800 dark:text-gray-100">Fecha registro:</span> <span class="text-gray-600 dark:text-gray-300">{{ $container->received_at?->format('d/m/Y H:i') ?? $container->created_at->format('d/m/Y H:i') }}</span></div>
                </div>

                @if($container->notes)
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 italic">{{ $container->notes }}</p>
                @endif

                {{-- Acciones de estatus --}}
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-wrap items-center gap-3">
                    <form method="POST" action="{{ route('containers.update-customs', $container) }}" class="flex items-center space-x-2">
                        @csrf @method('PATCH')
                        <select name="customs_status" class="border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 focus:ring-2 focus:ring-teal-500 text-gray-900 dark:text-gray-100">
                            @foreach(['pendiente', 'en_revision', 'liberado', 'retenido'] as $cs)
                                <option value="{{ $cs }}" {{ $container->customs_status === $cs ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cs)) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 text-sm transition">
                            <i class="fas fa-sync-alt mr-1"></i> Aduana
                        </button>
                    </form>

                    @if($container->status !== 'cerrado')
                        <form method="POST" action="{{ route('containers.close', $container) }}" onsubmit="return confirm('¿Cerrar este contenedor?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-500 text-sm transition">
                                <i class="fas fa-lock mr-1"></i> Cerrar contenedor
                            </button>
                        </form>
                    @endif

                    {{-- Link a inspección en móvil --}}
                    <div class="md:hidden flex flex-wrap gap-2">
                        <a href="{{ route('containers.inspection', $container) }}" class="px-3 py-2 bg-teal-600 text-white rounded-lg text-sm">
                            <i class="fas fa-tags mr-1"></i> Inspección
                        </a>
                        <a href="{{ route('containers.packing', $container) }}" class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm">
                            <i class="fas fa-box mr-1"></i> Empaque
                        </a>
                        <a href="{{ route('containers.pallets', $container) }}" class="px-3 py-2 bg-purple-600 text-white rounded-lg text-sm">
                            <i class="fas fa-pallet mr-1"></i> Tarimas
                        </a>
                    </div>
                </div>
            </div>

            {{-- Tabla de items --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                        <i class="fas fa-list-alt text-teal-500 mr-2"></i>Artículos del Packing List
                    </h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $container->items->count() }} artículos</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">#</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Código</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Descripción</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Barcode</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Declarado</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Recibido</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Cajas</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Peso bruto</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Empaque</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($container->items as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-3 py-3 text-gray-500">{{ $item->item_number }}</td>
                                    <td class="px-3 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $item->product_code ?? '—' }}</td>
                                    <td class="px-3 py-3 text-gray-800 dark:text-gray-200 max-w-xs">
                                        <div class="truncate" title="{{ $item->product_description }}">{{ $item->product_description }}</div>
                                        @if($item->product_description_cn)
                                            <div class="text-xs text-gray-400 truncate">{{ $item->product_description_cn }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 font-mono text-xs text-gray-500">{{ $item->barcode ?? '—' }}</td>
                                    <td class="px-3 py-3 text-center font-medium text-gray-800 dark:text-gray-200">{{ number_format($item->declared_qty) }}</td>
                                    <td class="px-3 py-3 text-center">
                                        @if($container->status !== 'cerrado')
                                            <form method="POST" action="{{ route('containers.update-item', $item) }}" class="inline-flex items-center space-x-1">
                                                @csrf @method('PATCH')
                                                <input type="number" name="received_qty" value="{{ $item->received_qty }}" min="0"
                                                       class="w-20 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-sm py-1 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-teal-500">
                                                <button type="submit" class="text-teal-600 hover:text-teal-800 dark:text-teal-400" title="Guardar">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="font-medium">{{ number_format($item->received_qty) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-center text-gray-600 dark:text-gray-300">{{ $item->carton_count }}</td>
                                    <td class="px-3 py-3 text-center text-gray-600 dark:text-gray-300">{{ number_format($item->gross_weight_kg, 1) }} kg</td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="text-xs text-gray-500">{{ $item->package_type ?? '—' }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        @php
                                            $itemBadge = match($item->status) {
                                                'conforme'  => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                'faltante'  => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                'sobrante'  => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                default     => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $itemBadge }}">
                                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-inbox text-3xl text-gray-300 dark:text-gray-600 mb-2 block"></i>
                                        No hay artículos. Cargue un Packing List o agregue manualmente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Agregar item manual --}}
            @if($container->status !== 'cerrado')
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">
                        <i class="fas fa-plus-circle text-teal-500 mr-2"></i>Agregar Artículo Manual
                    </h3>
                    <form method="POST" action="{{ route('containers.add-item', $container) }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Código producto</label>
                            <input type="text" name="product_code" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-teal-500" placeholder="Código">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Descripción *</label>
                            <input type="text" name="product_description" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-teal-500" placeholder="Descripción del artículo">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Barcode</label>
                            <input type="text" name="barcode" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-teal-500" placeholder="EAN/UPC">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Cantidad *</label>
                            <input type="number" name="declared_qty" required min="1" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-teal-500" placeholder="0">
                        </div>
                        <div>
                            <button type="submit" class="w-full px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-500 transition text-sm font-medium">
                                <i class="fas fa-plus mr-1"></i> Agregar
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
