<x-app-layout>
     <x-slot name="header">
         <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
             {{ __('Órdenes de Trabajo') }}
         </h2>
     </x-slot>

     <div class="py-12">
         <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
             {{-- Mensaje de éxito (si viene de otra acción) --}}
             @if (session('success'))
                 <div class="mb-4 p-4 bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded">
                     {{ session('success') }}
                 </div>
             @endif

             <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                 <div class="p-6 text-gray-900 dark:text-gray-100">
                     <div class="flex justify-between items-center mb-4">
                         <h3 class="text-lg font-medium">Listado de Órdenes</h3>
                         <a href="{{ route('work_orders.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                             Crear Nueva Orden
                         </a>
                     </div>

                     <div class="overflow-x-auto">
                         <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                             <thead class="bg-gray-50 dark:bg-gray-600">
                                 <tr>
                                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Folio</th>
                                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Proceso</th>
                                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha/Hora</th>
                                     <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                 </tr>
                             </thead>
                             <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                 @forelse ($workOrders as $order)
                                     <tr>
                                        @if ($order->status === 'Enviado')
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600 dark:text-red-400"><a href="{{ route('work_orders.show', $order) }}">{{ $order->folio }}</a></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->process }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->started_at ? $order->started_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                             <a href="{{ route('work_orders.show', $order) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">Ver Detalles</a>
                                                    @if ($order->status === 'Pendiente Escaneo' && $order->station === '01')
                                                        <a href="{{ route('work_orders.scanning', $order) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                                        <span class="text-black"> | </span> Continuar Escaneo
                                                        </a>
                                                    @endif   
                                         </td>
                                        @elseif($order->status === 'Pendiente Escaneo')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"><a href="{{ route('work_orders.show', $order) }}" class="text-green-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">{{ $order->folio }}</a></td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->process }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->started_at ? $order->started_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                             <a href="{{ route('work_orders.show', $order) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">Ver Detalles</a>
                                             @if ($order->status === 'Pendiente Escaneo' && $order->station === '01')
                                                <a href="{{ route('work_orders.scanning', $order) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                                <span class="text-black"> | </span> Continuar Escaneo
                                                </a>
                                            @endif   
                                         </td>
                                         @else
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"><a href="{{ route('work_orders.show', $order) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">{{ $order->folio }}</a></td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->process }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->started_at ? $order->started_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                             <a href="{{ route('work_orders.show', $order) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">Ver Detalles</a>
                                             @if ($order->status === 'Pendiente Escaneo' && $order->station === '01')
                                                <a href="{{ route('work_orders.scanning', $order) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                                <span class="text-black"> | </span> Continuar Escaneo
                                                </a>
                                            @endif   
                                         </td>
                                        @endif
                                         
                                     </tr>
                                 @empty
                                     <tr>
                                         <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No hay órdenes de trabajo registradas.</td>
                                     </tr>
                                 @endforelse
                             </tbody>
                         </table>
                     </div>
                     {{-- Enlaces de paginación si usaste paginate() --}}
                     <div class="mt-4">
                         {{ $workOrders->links() }}
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </x-app-layout>
 