<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalle de Conteo:') }} {{ $inventoryCount->folio }}
            </h2>
            <a href="{{ route('inventory.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                Volver a Inventarios
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Mensajes de sesión --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Información General del Conteo --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Información General</h3>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                            {{ $inventoryCount->status === 'completado' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
                            {{ $inventoryCount->status === 'en_proceso' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}
                            {{ $inventoryCount->status === 'cancelado' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : '' }}">
                            {{ $inventoryCount->readable_status }}
                        </span>
                    </div>

                    <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Folio:</dt>
                            <dd class="text-gray-900 dark:text-gray-100 font-mono">{{ $inventoryCount->folio }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Tipo:</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $inventoryCount->readable_type }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Usuario:</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $inventoryCount->user->name }}</dd>
                        </div>
                        @if($inventoryCount->station)
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Estación:</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $inventoryCount->station }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Fecha Inicio:</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $inventoryCount->started_at ? $inventoryCount->started_at->format('d/m/Y H:i') : 'N/A' }}</dd>
                        </div>
                        @if($inventoryCount->completed_at)
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Fecha Finalización:</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $inventoryCount->completed_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if($inventoryCount->notes)
                        <div class="mt-4 pt-4 border-t dark:border-gray-700">
                            <dt class="font-medium text-gray-500 dark:text-gray-400 mb-2">Notas:</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $inventoryCount->notes }}</dd>
                        </div>
                    @endif

                    {{-- Botón para continuar si está en proceso --}}
                    @if($inventoryCount->status === 'en_proceso')
                        <div class="mt-6 pt-4 border-t dark:border-gray-700">
                            <a href="{{ route('inventory.rfid-scan', $inventoryCount) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Continuar Escaneo RFID
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resumen de Resultados (solo si está completado) --}}
            @if($inventoryCount->status === 'completado')
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-full">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">Resumen de Resultados</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg text-center">
                                <div class="text-3xl font-bold text-blue-600 dark:text-blue-300">{{ $inventoryCount->expected_count ?? 'N/A' }}</div>
                                <div class="text-sm text-blue-700 dark:text-blue-200 mt-1">Esperados</div>
                            </div>
                            <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg text-center">
                                <div class="text-3xl font-bold text-green-600 dark:text-green-300">{{ $verifiedInstances ? $verifiedInstances->count() : 0 }}</div>
                                <div class="text-sm text-green-700 dark:text-green-200 mt-1">Verificados</div>
                            </div>
                            <div class="p-4 bg-red-50 dark:bg-red-900 rounded-lg text-center">
                                <div class="text-3xl font-bold text-red-600 dark:text-red-300">{{ $missingInstances ? $missingInstances->count() : 0 }}</div>
                                <div class="text-sm text-red-700 dark:text-red-200 mt-1">Faltantes</div>
                            </div>
                            <div class="p-4 bg-yellow-50 dark:bg-yellow-900 rounded-lg text-center">
                                <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-300">{{ $unexpectedInstances ? $unexpectedInstances->count() : 0 }}</div>
                                <div class="text-sm text-yellow-700 dark:text-yellow-200 mt-1">Inesperados</div>
                            </div>
                        </div>

                        {{-- Alerta si hay discrepancias --}}
                        @if($inventoryCount->discrepancy_count > 0)
                            <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-md">
                                <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200 mb-2">⚠️ Discrepancias Detectadas</h4>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    Se encontraron {{ $inventoryCount->discrepancy_count }} discrepancia(s) en este conteo.
                                    Revise las secciones de items faltantes e inesperados a continuación.
                                </p>
                            </div>
                        @else
                            <div class="mt-6 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-md">
                                <h4 class="text-sm font-semibold text-green-800 dark:text-green-200">✓ Sin Discrepancias</h4>
                                <p class="text-sm text-green-700 dark:text-green-300">
                                    Todos los items esperados fueron verificados correctamente.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Items Verificados --}}
                @if($verifiedInstances && $verifiedInstances->count() > 0)
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="max-w-full">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                ✓ Items Verificados ({{ $verifiedInstances->count() }})
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-600">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">EPC</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estación</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($verifiedInstances as $instance)
                                            <tr class="bg-green-50 dark:bg-green-900">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $instance->epc }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->product->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->current_station ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->status }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Items Faltantes --}}
                @if($missingInstances && $missingInstances->count() > 0)
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="max-w-full">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                ❌ Items Faltantes ({{ $missingInstances->count() }})
                            </h3>
                            <div class="mb-4 p-3 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded text-sm text-red-700 dark:text-red-300">
                                Estos items se esperaban pero no fueron detectados durante el escaneo RFID.
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-600">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">EPC</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estación</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($missingInstances as $instance)
                                            <tr class="bg-red-50 dark:bg-red-900">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $instance->epc }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->product->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->current_station ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->status }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Items Inesperados --}}
                @if($unexpectedInstances && $unexpectedInstances->count() > 0)
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="max-w-full">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                ⚠️ Items Inesperados ({{ $unexpectedInstances->count() }})
                            </h3>
                            <div class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded text-sm text-yellow-700 dark:text-yellow-300">
                                Estos items fueron detectados pero no se esperaban en este conteo.
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-600">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">EPC</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estación</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($unexpectedInstances as $instance)
                                            <tr class="bg-yellow-50 dark:bg-yellow-900">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $instance->epc }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->product->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->current_station ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->status }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
