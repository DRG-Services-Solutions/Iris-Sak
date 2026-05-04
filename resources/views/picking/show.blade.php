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
                    <form method="POST" action="{{ route('picking.start', $order) }}">@csrf<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm"><i class="fas fa-play mr-1"></i> Iniciar Surtido</button></form>
                @endif
                @if($order->status === 'completado' && !$order->dispatch)
                    <a href="{{ route('dispatch.create', ['picking_order_id' => $order->id]) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm"><i class="fas fa-truck mr-1"></i> Crear Despacho</a>
                @endif
            </div>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))<div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif

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
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white"><i class="fas fa-pallet text-orange-500 mr-2"></i>Tarimas a Preparar</h3>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($order->items->sortBy(fn($i) => $i->pallet->location?->code ?? 'ZZZ') as $item)
                        <div class="px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 {{ $item->status === 'preparado' ? 'bg-green-50 dark:bg-green-900/10' : ($item->status === 'cargado' ? 'bg-blue-50 dark:bg-blue-900/10' : '') }}">
                            <div>
                                <div class="flex items-center space-x-3">
                                    @if($item->status === 'pendiente')
                                        <i class="fas fa-circle text-yellow-400"></i>
                                    @elseif($item->status === 'preparado')
                                        <i class="fas fa-check-circle text-green-500"></i>
                                    @else
                                        <i class="fas fa-truck text-blue-500"></i>
                                    @endif
                                    <div>
                                        <p class="font-mono font-bold text-gray-900 dark:text-white">{{ $item->pallet->pallet_code }}</p>
                                        <p class="text-xs text-gray-500">
                                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $item->pallet->location?->code ?? 'Sin localidad' }}
                                            · {{ $item->pallet->container->packing_list_number }}
                                            · {{ $item->pallet->boxes->count() }} cajas
                                            · {{ $item->pallet->boxes->sum('quantity') }} pzas
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @php $iBadge = match($item->status) { 'preparado'=>'bg-green-100 text-green-800','cargado'=>'bg-blue-100 text-blue-800',default=>'bg-yellow-100 text-yellow-800' }; @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $iBadge }}">{{ ucfirst($item->status) }}</span>
                                @if($item->status === 'pendiente' && in_array($order->status, ['en_proceso']))
                                    <form method="POST" action="{{ route('picking.mark-prepared', $item) }}">@csrf @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs hover:bg-green-500"><i class="fas fa-check mr-1"></i> Preparado</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
