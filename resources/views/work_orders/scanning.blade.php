<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 p-3 rounded-lg shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        {{ __('Procesando Orden:') }} <span class="text-emerald-600 dark:text-emerald-400">{{ $workOrder->folio }}</span>
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Escaneo y registro de herramientas</p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-3">
                <div class="text-center px-4 py-2 bg-emerald-700 rounded-lg">
                    <p class="text-2xl font-bold text-white" id="scanned-count">{{ $instances->count() }}</p>
                    <p class="text-xs text-gray-200">Items Escaneados</p>
                </div>
                <a href="{{ route('work_orders.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-slate-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-200 hover:bg-slate-200 dark:hover:bg-gray-600 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Contenido --}}
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Información de la Orden --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-white">Detalles de la Orden</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Usuario --}}
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Usuario</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $workOrder->user->name }}</p>
                            </div>
                        </div>

                        {{-- Fecha --}}
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Iniciada</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $workOrder->started_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        {{-- Proceso --}}
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Proceso</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $workOrder->process }}</p>
                            </div>
                        </div>

                        {{-- Estado --}}
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Estado</p>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                    {{ $workOrder->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Estación (full width) --}}
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-slate-600 dark:text-slate-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Estación:</span>
                            <span class="ml-2 text-sm font-bold text-gray-900 dark:text-gray-100">{{ $workOrder->station }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sección de Escaneo --}}
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-lg shadow-xl border-2 border-emerald-500 dark:border-emerald-600 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <div>
                                <h3 class="text-lg font-bold text-white">Escanear Código de Barras</h3>
                                <p class="text-sm text-emerald-100">Utilice el lector para registrar items</p>
                            </div>
                        </div>
                        <div class="hidden md:flex items-center space-x-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg">
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-white">Listo para escanear</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="bg-emerald-600 p-4 rounded-xl shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <x-input-label for="barcode_input" :value="__('Código de Barras del Producto')" class="text-base font-semibold text-gray-800 dark:text-gray-200" />
                            <div class="mt-2 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <x-text-input 
                                    id="barcode_input" 
                                    name="barcode_input" 
                                    type="text" 
                                    class="pl-10 block w-full border-2 border-emerald-300 dark:border-emerald-600 focus:border-emerald-500 focus:ring-emerald-500 text-lg font-mono" 
                                    placeholder="Escanee o ingrese el código..." 
                                    autofocus />
                            </div>
                            <p class="mt-2 text-xs text-gray-600 dark:text-gray-400 flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Presione Enter o use el lector de código de barras
                            </p>
                        </div>
                    </div>

                    {{-- Feedback del escaneo --}}
                    <div id="scan-feedback" class="mt-4"></div>
                </div>
            </div>

            {{-- Lista de Items Escaneados --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <h3 class="text-lg font-semibold text-white">Lista de Items Escaneados</h3>
                        </div>
                        <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-bold text-white">
                            <span id="items-count-badge">{{ $instances->count() }}</span> items
                        </span>
                    </div>
                </div>

                {{-- Vista Desktop: Tabla --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                        <span>EPC  (Se usara luego para identificacion RFID)</span>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        <span>Producto</span>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Estado</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="scanned-items-list" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($instances as $instance)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-mono font-medium bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700">
                                            {{ $instance->epc }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-slate-600 to-slate-800 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $instance->product->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                            {{ $instance->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-state-row">
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-6 mb-4">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-2">No hay items escaneados</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Comience escaneando códigos de barras</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Vista Móvil: Cards --}}
                <div class="md:hidden p-4 space-y-3" id="scanned-items-list-mobile">
                    @forelse ($instances as $instance)
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="space-y-2">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">EPC</p>
                                    <p class="text-sm font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $instance->epc }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Producto</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $instance->product->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Estado</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                        {{ $instance->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div id="empty-state-mobile" class="text-center py-8">
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-6 mb-4 inline-flex">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">No hay items</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Escanee códigos</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Finalización del Proceso --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-white">Finalizar Proceso</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="bg-blue-600 p-3 rounded-xl shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Al finalizar el escaneo, la orden pasará al siguiente proceso en la cadena de producción.
                            </p>
                            <form method="POST" action="{{ route('work_orders.finalize', $workOrder) }}">
                                @csrf
                                @method('PUT')
                                <button type="submit" 
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold text-sm rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Finalizar Escaneo y Continuar') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <x-industrial-footer>
                Sistema de escaneo RFID - Trazabilidad de herramientas y componentes
            </x-industrial-footer>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM Cargado. Iniciando script de escaneo...');

            const barcodeInput = document.getElementById('barcode_input');
            const scannedItemsList = document.getElementById('scanned-items-list');
            const scannedItemsListMobile = document.getElementById('scanned-items-list-mobile');
            const scanFeedback = document.getElementById('scan-feedback');
            const scannedCount = document.getElementById('scanned-count');
            const itemsCountBadge = document.getElementById('items-count-badge');
            const emptyStateRow = document.getElementById('empty-state-row');
            const emptyStateMobile = document.getElementById('empty-state-mobile');
            const scanUrl = "{{ route('work_orders.scan', $workOrder) }}";
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;

            if (!barcodeInput) {
                console.error('ERROR: No se encontró el input de código de barras');
                showFeedback('Error interno: No se encontró el campo de código de barras.', 'error');
                return;
            }
            if (!csrfToken) {
                console.error('ERROR: No se encontró el token CSRF');
                showFeedback('Error interno: Falta configuración CSRF.', 'error');
                return;
            }

            function showFeedback(message, type = 'info') {
                scanFeedback.innerHTML = '';
                const alertDiv = document.createElement('div');
                
                const colors = {
                    success: 'bg-green-50 dark:bg-green-900/30 border-green-500 text-green-800 dark:text-green-200',
                    error: 'bg-red-50 dark:bg-red-900/30 border-red-500 text-red-800 dark:text-red-200',
                    info: 'bg-blue-50 dark:bg-blue-900/30 border-blue-500 text-blue-800 dark:text-blue-200'
                };
                
                const icons = {
                    success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                    error: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                    info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
                };
                
                alertDiv.className = `flex items-center p-4 rounded-lg border-l-4 ${colors[type]}`;
                alertDiv.innerHTML = `
                    <div class="flex-shrink-0">${icons[type]}</div>
                    <div class="ml-3 text-sm font-medium">${message}</div>
                `;
                
                scanFeedback.appendChild(alertDiv);
                
                if (type === 'success' || type === 'error') {
                    setTimeout(() => {
                        alertDiv.style.transition = 'opacity 0.5s';
                        alertDiv.style.opacity = '0';
                        setTimeout(() => alertDiv.remove(), 500);
                    }, 3000);
                }
            }

            function updateCounters() {
                const currentCount = scannedItemsList.querySelectorAll('tr:not(#empty-state-row)').length;
                if (scannedCount) scannedCount.textContent = currentCount;
                if (itemsCountBadge) itemsCountBadge.textContent = currentCount;
            }

            barcodeInput.addEventListener('keypress', function (event) {
                if (event.key === 'Enter' || event.keyCode === 13) {
                    event.preventDefault();
                    const barcode = barcodeInput.value.trim();

                    if (barcode === '') {
                        showFeedback('Por favor ingrese un código de barras', 'info');
                        return;
                    }

                    showFeedback('Procesando escaneo...', 'info');

                    fetch(scanUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ barcode: barcode })
                    })
                    .then(response => {
                        if (!response.ok && response.status !== 422) {
                            throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })

                    
                    .then(data => {
                        if (data.success) {
                            showFeedback('✓ Item registrado correctamente', 'success');
                            
                            // Remover estados vacíos si existen
                            if (emptyStateRow) emptyStateRow.remove();
                            if (emptyStateMobile) emptyStateMobile.remove();

                            const productName = data.instance.product ? data.instance.product.name : 'Producto Desconocido';

                            // Desktop: Añadir fila a la tabla
                            const newRow = document.createElement('tr');
                            newRow.className = 'hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 animate-fade-in';
                            newRow.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-mono font-medium bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700">
                                        ${data.instance.epc}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-slate-600 to-slate-800 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">${productName}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                        ${data.instance.status}
                                    </span>
                                </td>
                            `;
                            scannedItemsList.appendChild(newRow);

                            // Móvil: Añadir card
                            if (scannedItemsListMobile) {
                                const newCard = document.createElement('div');
                                newCard.className = 'bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700 animate-fade-in';
                                newCard.innerHTML = `
                                    <div class="space-y-2">
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">EPC</p>
                                            <p class="text-sm font-mono font-semibold text-gray-900 dark:text-gray-100">${data.instance.epc}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Producto</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">${productName}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Estado</p>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                ${data.instance.status}
                                            </span>
                                        </div>
                                    </div>
                                `;
                                scannedItemsListMobile.appendChild(newCard);
                            }

                            updateCounters();
                        } else {
                            showFeedback(data.message || 'Error al procesar el escaneo', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showFeedback('Error de conexión: ' + error.message, 'error');
                    })
                    .finally(() => {
                        barcodeInput.value = '';
                        barcodeInput.focus();
                    });
                }
            });

            barcodeInput.focus();
            console.log('Script de escaneo inicializado correctamente');
        });
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>
    @endpush
</x-app-layout>