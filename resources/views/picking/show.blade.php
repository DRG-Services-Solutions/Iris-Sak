<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('picking.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"><i class="fas fa-arrow-left text-lg"></i></a>
                <div class="bg-gradient-to-br from-orange-500 to-orange-700 p-3 rounded-lg shadow-lg"><i class="fas fa-clipboard-check text-white text-xl"></i></div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">{{ $order->order_number }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $order->client_name }} → {{ $order->destination }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                @if($order->status === 'pendiente')
                    <form method="POST" action="{{ route('picking.start', $order) }}">@csrf<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm shadow hover:bg-blue-500 transition"><i class="fas fa-play mr-1"></i> Iniciar Surtido</button></form>
                @endif
                @if($order->status === 'completado' && !$order->dispatch)
                    <a href="{{ route('dispatch.create', ['picking_order_id' => $order->id]) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm shadow hover:bg-green-500 transition"><i class="fas fa-truck mr-1"></i> Crear Despacho</a>
                @endif
            </div>
        </div>
    </x-slot>
    
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))<div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif
            @if(session('error'))<div class="p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>@endif

            {{-- Info de la orden --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div><span class="font-semibold text-gray-800 dark:text-gray-100">Estatus:</span>
                        @php $oBadge = match($order->status) { 'pendiente'=>'bg-yellow-100 text-yellow-800','en_proceso'=>'bg-blue-100 text-blue-800','completado'=>'bg-green-100 text-green-800',default=>'bg-gray-100 text-gray-800' }; @endphp
                        <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $oBadge }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
                    </div>
                    <div><span class="font-semibold">Prioridad:</span> <span class="{{ $order->priority === 'urgente' ? 'text-red-600 font-bold' : 'text-gray-600' }}">{{ ucfirst($order->priority) }}</span></div>
                    <div><span class="font-semibold">Asignado:</span> <span class="text-gray-600 dark:text-gray-300">{{ $order->assignee?->name ?? '—' }}</span></div>
                    <div><span class="font-semibold">Creado:</span> <span class="text-gray-600 dark:text-gray-300">{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
                </div>
                @if($order->notes)<p class="mt-3 text-sm text-gray-500 italic">{{ $order->notes }}</p>@endif
            </div>

            {{-- Tarimas a surtir --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white"><i class="fas fa-pallet text-orange-500 mr-2"></i>Tarimas a Preparar</h3>
                </div>
                
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($order->items->sortBy(fn($i) => $i->pallet->location?->code ?? 'ZZZ') as $item)
                        @php 
                            // 1. Agrupamos las cajas físicas para saber cuántos artículos diferentes hay
                            $groupedBoxes = $item->pallet->boxes->groupBy('container_item_id');
                            $isMixed = $groupedBoxes->count() > 1; 
                            
                            // 2. Preparamos los datos para que Alpine sepa la capacidad máxima por SKU
                            $skuData = $groupedBoxes->mapWithKeys(function($boxes, $id) {
                                return [$id => $boxes->count()];
                            })->toJson();
                        @endphp

                        {{-- Contenedor del Ítem con Alpine JS --}}
                        <div class="px-6 py-4 flex flex-col {{ $item->status === 'preparado' ? 'bg-green-50 dark:bg-green-900/10' : ($item->status === 'cargado' ? 'bg-blue-50 dark:bg-blue-900/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30 transition') }}"
                             x-data="{ 
                                 showPartialForm: false, 
                                 skuData: {{ $skuData }}, 
                                 selectedSku: '', 
                                 maxQty: 0,
                                 updateMax() {
                                     this.maxQty = this.skuData[this.selectedSku] || 0;
                                     $refs.qtyInput.value = '';
                                 }
                             }">
                             
                            {{-- Fila Principal (Información y Botones) --}}
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                <div class="flex items-center space-x-3">
                                    @if($item->status === 'pendiente')
                                        <i class="fas fa-circle text-yellow-400"></i>
                                    @elseif($item->status === 'preparado')
                                        <i class="fas fa-check-circle text-green-500"></i>
                                    @else
                                        <i class="fas fa-truck text-blue-500"></i>
                                    @endif
                                    
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <p class="font-mono font-bold text-gray-900 dark:text-white">{{ $item->pallet->pallet_code }}</p>
                                            @if($isMixed)
                                                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded text-[10px] font-bold">MIXTA ({{ $groupedBoxes->count() }} SKUs)</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400 rounded text-[10px] font-bold">PURA</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            <i class="fas fa-map-marker-alt mr-1 text-emerald-500"></i><span class="font-semibold">{{ $item->pallet->location?->code ?? 'Sin localidad' }}</span>
                                            · {{ $item->pallet->container->container_seal_number }}
                                            · {{ $item->pallet->boxes->count() }} cajas totales
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    @php $iBadge = match($item->status) { 'preparado'=>'bg-green-100 text-green-800','cargado'=>'bg-blue-100 text-blue-800',default=>'bg-yellow-100 text-yellow-800' }; @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $iBadge }}">{{ ucfirst($item->status) }}</span>
                                    
                                    @if($item->status === 'pendiente' && $order->status === 'en_proceso')
                                        
                                        @if(!$isMixed)
                                            {{-- Tarima Pura: Surtido Directo y Rápido --}}
                                            <form method="POST" action="{{ route('picking.mark-prepared', $item) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="pick_type" value="full_pallet">
                                                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-bold hover:bg-green-500 shadow-sm transition">
                                                    <i class="fas fa-check mr-1"></i> Surtir Tarima
                                                </button>
                                            </form>
                                        @else
                                            {{-- Tarima Mixta: Botón para abrir formulario --}}
                                            <button @click="showPartialForm = !showPartialForm" type="button" class="px-3 py-1.5 bg-orange-500 text-white rounded-lg text-xs font-bold hover:bg-orange-400 shadow-sm transition">
                                                <i class="fas fa-box-open mr-1"></i> Surtido Parcial
                                            </button>
                                        @endif
                                        
                                    @endif
                                </div>
                            </div>

                            {{-- Formulario Desplegable para Surtido Parcial (Solo Tarimas Mixtas) --}}
                            @if($isMixed && $item->status === 'pendiente' && $order->status === 'en_proceso')
                                <div x-show="showPartialForm" x-collapse x-cloak>
                                    <div class="mt-4 p-4 bg-orange-50 dark:bg-gray-900 border border-orange-200 dark:border-gray-600 rounded-lg">
                                        <form method="POST" action="{{ route('picking.mark-prepared', $item) }}" class="flex flex-col md:flex-row gap-3 items-end">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="pick_type" value="partial">

                                            <div class="flex-1 w-full">
                                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Artículo a extraer</label>
                                                <select name="container_item_id" x-model="selectedSku" @change="updateMax" required class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-orange-500 focus:border-orange-500">
                                                    <option value="">Selecciona el producto...</option>
                                                    @foreach($groupedBoxes as $id => $boxes)
                                                        @php $cItem = $boxes->first()->containerItem; @endphp
                                                        <option value="{{ $id }}">{{ $cItem->barcode }} - {{ Str::limit($cItem->product_description, 40) }} (Disp: {{ $boxes->count() }} cajas)</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="w-full md:w-32">
                                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Cajas (Max: <span x-text="maxQty"></span>)</label>
                                                <input type="number" name="quantity" x-ref="qtyInput" min="1" :max="maxQty" required class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-orange-500 focus:border-orange-500 font-bold text-center">
                                            </div>

                                            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-bold hover:bg-orange-500 transition shadow-sm h-[42px] flex items-center justify-center">
                                                Extraer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                            
                        </div>
                    @endforeach
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>