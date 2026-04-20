<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('containers.show', $container) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-box text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">Empaque en Cajas</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Contenedor {{ $container->container_number }}</p>
                </div>
            </div>
            <a href="{{ route('containers.pallets', $container) }}" class="hidden md:inline-flex items-center px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-500 transition text-sm font-medium">
                <i class="fas fa-pallet mr-1"></i> Ir a Tarimas
            </a>
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

            {{-- KPIs --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_boxes'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total cajas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $stats['boxes_closed'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Cerradas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['boxes_on_pallet'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">En tarima</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-slate-700 dark:text-slate-300">{{ number_format($stats['total_packed_pcs']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Piezas empacadas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ number_format($container->declared_qty) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Piezas declaradas</p>
                </div>
            </div>

            {{-- Formulario de creación de cajas --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-boxes-stacked text-indigo-500 mr-2"></i>Crear Cajas
                </h3>
                <form method="POST" action="{{ route('containers.create-boxes', $container) }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Artículo</label>
                        <select name="container_item_id" required
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2.5 px-3 focus:ring-2 focus:ring-indigo-500">
                            <option value="">Seleccionar artículo...</option>
                            @foreach($container->items as $item)
                                @php
                                    $packedQty = $item->boxes->sum('quantity');
                                    $remaining = $item->declared_qty - $packedQty;
                                @endphp
                                <option value="{{ $item->id }}">
                                    {{ $item->product_code ? $item->product_code . ' — ' : '' }}{{ $item->product_description }}
                                    ({{ number_format($remaining) }} pzas restantes)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Piezas por caja</label>
                        <input type="number" name="pieces_per_box" required min="1" value="20"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2.5 px-3 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Cantidad de cajas</label>
                        <input type="number" name="box_count" required min="1" max="500" value="1"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2.5 px-3 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500 transition text-sm font-medium">
                            <i class="fas fa-box mr-1"></i> Crear Cajas
                        </button>
                    </div>
                </form>
            </div>

            {{-- Link móvil a tarimas --}}
            <div class="md:hidden">
                <a href="{{ route('containers.pallets', $container) }}" class="flex items-center justify-center w-full px-4 py-3 bg-purple-600 text-white rounded-lg text-sm font-medium">
                    <i class="fas fa-pallet mr-2"></i> Ir a Armado de Tarimas
                </a>
            </div>

            {{-- Tabla de cajas creadas --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                        <i class="fas fa-box text-indigo-500 mr-2"></i>Cajas Creadas
                    </h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $container->boxes->count() }} cajas</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Código</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Artículo</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Piezas</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Estatus</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Tarima</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Creado por</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($container->boxes->sortByDesc('created_at') as $box)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 font-mono font-bold text-gray-900 dark:text-white">{{ $box->box_code }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300 max-w-xs truncate">{{ $box->containerItem?->product_description ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center font-medium text-gray-800 dark:text-gray-200">{{ $box->quantity }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $boxBadge = match($box->status) {
                                                'abierta'    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                'cerrada'    => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                'en_tarima'  => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                                default      => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $boxBadge }}">
                                            {{ ucfirst(str_replace('_', ' ', $box->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs font-mono">{{ $box->pallet?->pallet_code ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $box->creator?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if(!$box->isAssignedToPallet())
                                            <form method="POST" action="{{ route('boxes.destroy', $box) }}" onsubmit="return confirm('¿Eliminar caja {{ $box->box_code }}?')" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">En tarima</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <i class="fas fa-box-open text-4xl text-gray-300 dark:text-gray-600 mb-3 block"></i>
                                        <p class="text-gray-500 dark:text-gray-400 font-medium">No hay cajas creadas aún</p>
                                        <p class="text-sm text-gray-400 mt-1">Seleccione un artículo y defina las piezas por caja arriba</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
