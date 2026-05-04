<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-green-600 to-green-800 p-3 rounded-lg shadow-lg"><i class="fas fa-truck text-white text-xl"></i></div>
                <div><h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Despachos</h2><p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Carga y salida de mercancía</p></div>
            </div>
            <a href="{{ route('dispatch.create') }}" class="px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-500 transition text-sm font-medium"><i class="fas fa-plus mr-1"></i> Nuevo Despacho</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>@endif
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Despacho</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Orden Surtido</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Transporte</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Destino</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estatus</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($dispatches as $d)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 font-mono font-bold text-gray-900 dark:text-white">{{ $d->dispatch_number }}</td>
                                    <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $d->pickingOrder->order_number }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ ucfirst(str_replace('_',' ',$d->transport_type)) }} {{ $d->plates ? '· '.$d->plates : '' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $d->destination }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @php $dBadge = match($d->status) { 'preparando'=>'bg-yellow-100 text-yellow-800','cargado'=>'bg-blue-100 text-blue-800','despachado'=>'bg-green-100 text-green-800','cancelado'=>'bg-red-100 text-red-800',default=>'bg-gray-100 text-gray-800' }; @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $dBadge }}">{{ ucfirst($d->status) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 text-center"><a href="{{ route('dispatch.show', $d) }}" class="text-green-600 hover:text-green-800"><i class="fas fa-eye"></i></a></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500"><i class="fas fa-truck text-4xl text-gray-300 mb-3 block"></i>No hay despachos registrados</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($dispatches->hasPages())<div class="px-6 py-4 border-t">{{ $dispatches->links() }}</div>@endif
            </div>
        </div>
    </div>
</x-app-layout>
