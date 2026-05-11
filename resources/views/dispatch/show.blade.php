<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('dispatch.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"><i class="fas fa-arrow-left text-lg"></i></a>
                <div class="bg-gradient-to-br from-green-600 to-green-800 p-3 rounded-lg shadow-lg"><i class="fas fa-truck text-white text-xl"></i></div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">{{ $dispatch->dispatch_number }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $dispatch->destination }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                @if($dispatch->status === 'preparando')
                    <form method="POST" action="{{ route('dispatch.mark-loaded', $dispatch) }}">@csrf @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm" onclick="return confirm('¿Confirmar carga al transporte?')"><i class="fas fa-dolly mr-1"></i> Confirmar Carga</button>
                    </form>
                @endif
                @if($dispatch->status === 'cargado')
                    <form method="POST" action="{{ route('dispatch.mark-dispatched', $dispatch) }}">@csrf @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm" onclick="return confirm('¿Confirmar salida del transporte?')"><i class="fas fa-shipping-fast mr-1"></i> Confirmar Despacho</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))<div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div><span class="font-semibold text-gray-800 dark:text-gray-100">Estatus:</span>
                        @php $dBadge = match($dispatch->status) { 'preparando'=>'bg-yellow-100 text-yellow-800','cargado'=>'bg-blue-100 text-blue-800','despachado'=>'bg-green-100 text-green-800',default=>'bg-gray-100 text-gray-800' }; @endphp
                        <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $dBadge }}">{{ ucfirst($dispatch->status) }}</span>
                    </div>
                    <div><span class="font-semibold">Transporte:</span> <span class="text-gray-600 dark:text-gray-300">{{ ucfirst(str_replace('_',' ',$dispatch->transport_type)) }}</span></div>
                    <div><span class="font-semibold">Operador:</span> <span class="text-gray-600 dark:text-gray-300">{{ $dispatch->driver_name ?? '—' }}</span></div>
                    <div><span class="font-semibold">Placas:</span> <span class="text-gray-600 dark:text-gray-300">{{ $dispatch->plates ?? '—' }}</span></div>
                    <div><span class="font-semibold">Orden:</span> <a href="{{ route('picking.show', $dispatch->pickingOrder) }}" class="text-orange-600 hover:underline">{{ $dispatch->pickingOrder->order_number }}</a></div>
                    <div><span class="font-semibold">Creado por:</span> <span class="text-gray-600 dark:text-gray-300">{{ $dispatch->dispatchedBy->name }}</span></div>
                    @if($dispatch->loaded_at)<div><span class="font-semibold">Cargado:</span> <span class="text-gray-600 dark:text-gray-300">{{ $dispatch->loaded_at->format('d/m/Y H:i') }}</span></div>@endif
                    @if($dispatch->dispatched_at)<div><span class="font-semibold">Despachado:</span> <span class="text-gray-600 dark:text-gray-300">{{ $dispatch->dispatched_at->format('d/m/Y H:i') }}</span></div>@endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white"><i class="fas fa-pallet text-green-500 mr-2"></i>Mercancía en este despacho</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-1/4">Tarima / Origen</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Detalle de Carga</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Localidad origen</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Cajas a enviar</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Piezas a enviar</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @php $totalBoxes = 0; $totalPcs = 0; @endphp
                            @foreach($dispatch->pickingOrder->items as $item)
                                @php 
                                    // Evaluamos qué cajas mostrar: TODAS (si fue tarima completa) o SOLO LAS SELECCIONADAS (si fue parcial)
                                    $boxesToShip = $item->pick_type === 'full_pallet' 
                                        ? $item->pallet->boxes 
                                        : \App\Models\Box::where('picking_order_id', $dispatch->picking_order_id)
                                            ->where('container_item_id', $item->container_item_id)
                                            ->get();

                                    $itemBoxesCount = $boxesToShip->count();
                                    $itemPcsCount = $boxesToShip->sum('quantity');

                                    $totalBoxes += $itemBoxesCount; 
                                    $totalPcs += $itemPcsCount; 
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    {{-- Columna 1: Origen (Tarima) --}}
                                    <td class="px-4 py-4 align-top">
                                        <div class="flex flex-col">
                                            <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $item->pallet->pallet_code }}</span>
                                            <span class="text-xs text-gray-500 mt-1" title="Contenedor de origen">
                                                <i class="fas fa-ship mr-1 text-teal-500"></i>{{ $item->pallet->container->container_seal_number }}
                                            </span>
                                            @if($item->pick_type === 'partial')
                                                <span class="inline-flex mt-2 px-2 py-0.5 bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 rounded text-[10px] font-bold w-max">
                                                    Surtido Parcial
                                                </span>
                                            @else
                                                <span class="inline-flex mt-2 px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded text-[10px] font-bold w-max">
                                                    Tarima Completa
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    {{-- Columna 2: Detalle de Carga (Artículos) --}}
                                    <td class="px-4 py-4 align-top">
                                        @if($item->pick_type === 'partial')
                                            {{-- Muestra solo el producto solicitado en el surtido parcial --}}
                                            <div class="flex flex-col">
                                                <span class="font-mono font-bold text-xs text-gray-800 dark:text-gray-200">{{ $item->containerItem->barcode ?? 'Sin código' }}</span>
                                                <span class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 line-clamp-2" title="{{ $item->containerItem->product_description }}">{{ $item->containerItem->product_description }}</span>
                                            </div>
                                        @else
                                            {{-- Muestra un resumen agrupado de lo que trae la tarima completa --}}
                                            <div class="space-y-2">
                                                @foreach($boxesToShip->groupBy('container_item_id') as $cItemId => $boxes)
                                                    @php $cItem = $boxes->first()->containerItem; @endphp
                                                    <div class="flex items-start justify-between text-xs bg-gray-50 dark:bg-gray-800 p-2 rounded border border-gray-100 dark:border-gray-600">
                                                        <div class="flex flex-col flex-1 pr-2">
                                                            <span class="font-mono font-bold text-gray-800 dark:text-gray-200">{{ $cItem->barcode ?? 'Sin código' }}</span>
                                                            <span class="text-gray-500 dark:text-gray-400 line-clamp-1 mt-0.5" title="{{ $cItem->product_description }}">{{ $cItem->product_description }}</span>
                                                        </div>
                                                        <div class="flex flex-col text-right items-end font-medium">
                                                            <span class="bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 rounded text-slate-700 dark:text-slate-300">{{ $boxes->count() }} cjs</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Columna 3: Localidad --}}
                                    <td class="px-4 py-4 text-center align-top">
                                        <span class="inline-flex items-center px-2 py-1 rounded bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold">
                                            <i class="fas fa-map-marker-alt mr-1 text-emerald-500"></i>
                                            {{ $item->pallet->location?->code ?? '—' }}
                                        </span>
                                    </td>

                                    {{-- Columnas 4 y 5: Cajas y Piezas Reales --}}
                                    <td class="px-4 py-4 text-center align-top text-lg font-bold text-gray-800 dark:text-gray-200">{{ $itemBoxesCount }}</td>
                                    <td class="px-4 py-4 text-center align-top font-medium text-teal-600 dark:text-teal-400">{{ number_format($itemPcsCount) }}</td>
                                    
                                    {{-- Columna 6: Estatus --}}
                                    <td class="px-4 py-4 text-center align-top">
                                        @php $iBadge = match($item->status) { 'preparado'=>'bg-green-100 text-green-800','cargado'=>'bg-blue-100 text-blue-800',default=>'bg-yellow-100 text-yellow-800' }; @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold shadow-sm {{ $iBadge }}">{{ ucfirst($item->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 dark:bg-gray-700 font-bold border-t-2 border-gray-300 dark:border-gray-600">
                                <td colspan="3" class="px-4 py-4 text-right text-gray-800 dark:text-white uppercase tracking-wider text-xs">Total de Envío</td>
                                <td class="px-4 py-4 text-center text-lg text-gray-900 dark:text-white">{{ $totalBoxes }}</td>
                                <td class="px-4 py-4 text-center text-lg text-green-600 dark:text-green-400">{{ number_format($totalPcs) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
