<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-orange-500 to-orange-700 p-3 rounded-lg shadow-lg"><i class="fas fa-clipboard-list text-white text-xl"></i></div>
                <div><h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Lista de Surtido</h2><p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Órdenes de preparación (Picking)</p></div>
            </div>
            <a href="{{ route('picking.create') }}" class="px-4 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-500 transition text-sm font-medium"><i class="fas fa-plus mr-1"></i> Nueva Orden</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))<div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <form method="GET" class="flex flex-col md:flex-row gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por orden, cliente, destino..." class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100">
                    <select name="status" class="border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100">
                        <option value="">Todos</option><option value="pendiente" {{ request('status')==='pendiente'?'selected':'' }}>Pendiente</option><option value="en_proceso" {{ request('status')==='en_proceso'?'selected':'' }}>En proceso</option><option value="completado" {{ request('status')==='completado'?'selected':'' }}>Completado</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-lg text-sm"><i class="fas fa-filter mr-1"></i> Filtrar</button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Orden</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Destino</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tarimas</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Prioridad</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estatus</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Asignado a</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 font-mono font-bold text-gray-900 dark:text-white">{{ $order->order_number }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $order->client_name }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $order->destination }}</td>
                                    <td class="px-4 py-3 text-center font-medium">{{ $order->items_count }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $order->priority === 'urgente' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($order->priority) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @php $oBadge = match($order->status) { 'pendiente'=>'bg-yellow-100 text-yellow-800','en_proceso'=>'bg-blue-100 text-blue-800','completado'=>'bg-green-100 text-green-800','cancelado'=>'bg-red-100 text-red-800',default=>'bg-gray-100 text-gray-800' }; @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $oBadge }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $order->assignee?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 text-center"><a href="{{ route('picking.show', $order) }}" class="text-orange-600 hover:text-orange-800"><i class="fas fa-eye"></i></a></td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="px-6 py-12 text-center text-gray-500"><i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3 block"></i>No hay órdenes de surtido</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($orders->hasPages())<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">{{ $orders->links() }}</div>@endif
            </div>
        </div>
    </div>
</x-app-layout>
