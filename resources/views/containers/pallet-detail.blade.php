<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('containers.pallets', $pallet->container) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div class="bg-gradient-to-br from-purple-600 to-purple-800 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-qrcode text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">{{ $pallet->pallet_code }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Etiqueta Maestra de Tarima</p>
                </div>
            </div>
            <button onclick="window.print()" class="hidden md:inline-flex items-center px-4 py-2.5 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition text-sm font-medium print:hidden">
                <i class="fas fa-print mr-1"></i> Imprimir
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Tarjeta de etiqueta maestra --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border-2 border-purple-200 dark:border-purple-800 overflow-hidden" id="pallet-label">

                {{-- Header --}}
                <div class="bg-purple-600 text-white px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wider opacity-80">Tarima</p>
                            <h3 class="text-3xl font-bold tracking-wide">{{ $pallet->pallet_code }}</h3>
                        </div>
                        <div class="text-right">
                            <p class="text-xs uppercase tracking-wider opacity-80">Contenedor</p>
                            <p class="text-lg font-bold">{{ $pallet->container->container_seal_number }}</p>
                       </div>
                    </div>
                </div>

                {{-- Resumen --}}
                <div class="px-6 py-4 grid grid-cols-3 gap-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-purple-600">{{ $pallet->boxes->count() }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Cajas</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-indigo-600">{{ number_format($pallet->boxes->sum('quantity')) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Piezas</p>
                    </div>
                    <div class="text-center">
                        @php
                            $palletStatusBadge = $pallet->status === 'cerrada'
                                ? 'bg-green-100 text-green-800'
                                : 'bg-yellow-100 text-yellow-800';
                        @endphp
                        <span class="inline-flex px-3 py-1 rounded-full text-sm font-bold {{ $palletStatusBadge }}">
                            {{ ucfirst($pallet->status) }}
                        </span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold mt-1">Estatus</p>
                    </div>
                </div>

                {{-- Detalle del contenido --}}
                <div class="px-6 py-4">
                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider mb-3">Contenido</h4>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-2 text-xs font-semibold text-gray-500 uppercase">Producto</th>
                                <th class="text-center py-2 text-xs font-semibold text-gray-500 uppercase">Cajas</th>
                                <th class="text-center py-2 text-xs font-semibold text-gray-500 uppercase">Piezas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $grouped = $pallet->boxes->groupBy('container_item_id');
                            @endphp
                            @foreach($grouped as $itemId => $groupedBoxes)
                                @php $item = $groupedBoxes->first()->containerItem; @endphp
                                <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                    <td class="py-2">
                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $item?->product_description ?? '—' }}</span>
                                        @if($item?->product_code)
                                            <span class="text-xs text-gray-400 ml-1">({{ $item->barcode }})</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-center font-medium text-gray-700 dark:text-gray-300">{{ $groupedBoxes->count() }}</td>
                                    <td class="py-2 text-center font-bold text-indigo-600">{{ number_format($groupedBoxes->sum('quantity')) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                <td class="py-2 font-bold text-gray-800 dark:text-gray-200">TOTAL</td>
                                <td class="py-2 text-center font-bold text-gray-800 dark:text-gray-200">{{ $pallet->boxes->count() }}</td>
                                <td class="py-2 text-center font-bold text-indigo-600 text-lg">{{ number_format($pallet->boxes->sum('quantity')) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Lista de cajas --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider mb-2">Cajas incluidas</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($pallet->boxes->sortBy('box_code') as $box)
                            <span class="inline-flex items-center px-2 py-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-xs font-mono">
                                {{ $box->containerItem?->barcode ?? 'Sin código' }}
                                <span class="text-gray-400 ml-1">({{ $box->quantity }})</span>
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Trazabilidad --}}
                <div class="px-6 py-3 bg-gray-100 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 flex flex-wrap items-center justify-between gap-2">
                    <span><i class="fas fa-ship mr-1"></i>{{ $pallet->container->container_number }}</span>
                    <span><i class="fas fa-user mr-1"></i>{{ $pallet->creator?->name ?? '—' }}</span>
                    <span><i class="fas fa-calendar mr-1"></i>{{ $pallet->created_at->format('d/m/Y H:i') }}</span>
                    @if($pallet->closed_at)
                        <span><i class="fas fa-lock mr-1"></i>Cerrada {{ $pallet->closed_at->format('d/m/Y H:i') }}</span>
                    @endif
                </div>
            </div>

            {{-- Botón imprimir móvil --}}
            <div class="mt-4 print:hidden">
                <a href="{{ route('containers.label-4x2', $pallet) }}" target="_blank" class="hidden md:inline-flex items-center px-4 py-2.5 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition text-sm font-medium print:hidden">
                    <i class="fas fa-print mr-1"></i> Imprimir
                </a>
            </div>

        </div>
    </div>

    @push('scripts')
    <style>
        @media print {
            body * { visibility: hidden; }
            #pallet-label, #pallet-label * { visibility: visible; }
            #pallet-label { position: absolute; left: 0; top: 0; width: 100%; border: 2px solid #000 !important; }
        }
    </style>
    @endpush
</x-app-layout>
