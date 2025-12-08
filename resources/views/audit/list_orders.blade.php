<x-app-layout>
<x-slot name="header">
<div class="flex justify-between items-center">
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
{{ __('Auditoría de Embarque - Órdenes Pendientes') }}
</h2>
<a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
&larr; Volver al Dashboard
</a>
</div>
</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensaje de éxito (si viene de otra acción como completar una auditoría) --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            {{-- Mensaje de error --}}
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Órdenes 'Enviadas' Pendientes de Auditoría RFID</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Folio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Proceso Actual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estación Actual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Marcada 'Enviado'</th>
                                    <th class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($ordersToAudit as $order)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $order->folio }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->process }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->station }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->updated_at->format('d/m/Y H:i') }}</td> {{-- O completed_at si lo usas para marcar 'Enviado' --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('audit.work_orders.rfid_screen', $order) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                Iniciar Auditoría RFID
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center italic">No hay órdenes pendientes de auditoría.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Enlaces de paginación si usaste paginate() en el controlador --}}
                    <div class="mt-4">
                        {{ $ordersToAudit->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>