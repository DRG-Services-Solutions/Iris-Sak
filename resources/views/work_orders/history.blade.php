<x-app-layout>
    {{-- Encabezado: Indica de qué orden es el historial --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Historial de Actividad - Orden:') }} {{ $workOrder->folio }}
            </h2>
            {{-- Enlace para volver a los detalles de la orden --}}
            <a href="{{ route('work_orders.show', $workOrder) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Volver a Detalles de Orden
            </a>
        </div>
    </x-slot>

    {{-- Contenido Principal --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Registros de Actividad</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha y Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item Afectado (EPC / Producto)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Detalles</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                {{-- Iteramos sobre los logs paginados pasados desde el controlador --}}
                                @forelse ($activityLogs as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->user?->name ?? 'Sistema' }}</td> {{-- Muestra nombre o 'Sistema' si user_id es null --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $log->readable_action }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($log->productInstance) {{-- Verifica si hay una instancia asociada --}}
                                                <span class="font-mono text-xs">{{ $log->productInstance->epc }}</span>
                                                <span class="block text-xs text-gray-500">{{ $log->productInstance->product?->name ?? '(Producto no encontrado)' }}</span>
                                            @else
                                                N/A {{-- No aplica a una instancia específica (ej. ORDER_RELEASED) --}}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-xs"> {{-- Hice la fuente un poco más pequeña --}}
                                            @if($log->details && is_array($log->details) && count($log->details) > 0) {{-- Verificar que details sea un array no vacío --}}
                                                <ul class="list-none space-y-1"> 
                                                    @foreach($log->details as $key => $value)
                                                        <li>
                                                            {{-- Intenta hacer la clave más legible: ej. 'new_status' -> 'New Status' --}}
                                                            <span class="font-semibold text-gray-600 dark:text-gray-400">{{ Illuminate\Support\Str::headline($key) }}:</span>
                                                            {{-- Muestra el valor (si es otro array/objeto, lo muestra como JSON simple) --}}
                                                            <span class="text-gray-800 dark:text-gray-200">{{ is_array($value) || is_object($value) ? json_encode($value) : $value }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                - {{-- Muestra un guión si no hay detalles o está vacío --}}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    {{-- Mensaje si no hay logs para esta orden --}}
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center italic">No hay registros de actividad para esta orden de trabajo.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Enlaces de Paginación (si usaste paginate() en el controlador) --}}
                    <div class="mt-4">
                        {{ $activityLogs->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>