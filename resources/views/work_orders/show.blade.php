<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-3 rounded-lg shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        {{ __('Trabajo No:') }} <span class="text-blue-600 dark:text-blue-400">{{ $workOrder->folio }}</span>
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Detalles y verificación RFID</p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-3">
                <a href="{{ route('work_orders.history', $workOrder) }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 border border-transparent rounded-lg font-medium text-sm text-white transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Historial
                </a>
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

            {{-- Información General de la Orden --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-white">Información General</h3>
                        </div>
                        @if($workOrder->status === 'Enviado')
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                {{ $workOrder->status }}
                            </span>
                        @elseif($workOrder->status === 'Pendiente Escaneo')
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                {{ $workOrder->status }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                {{ $workOrder->status }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {{-- Folio --}}
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Folio</dt>
                                <dd class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $workOrder->folio }}</dd>
                            </div>
                        </div>

                        {{-- Usuario --}}
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Usuario</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $workOrder->user->name }}</dd>
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
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Proceso</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $workOrder->process }}</dd>
                            </div>
                        </div>

                        {{-- Estación --}}
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Estación</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $workOrder->station }}</dd>
                            </div>
                        </div>

                        {{-- Fecha Inicio --}}
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Iniciada</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $workOrder->started_at ? $workOrder->started_at->format('d/m/Y H:i') : 'N/A' }}
                                </dd>
                            </div>
                        </div>

                        {{-- Fecha Completada --}}
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Completada</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $workOrder->completed_at ? $workOrder->completed_at->format('d/m/Y H:i') : 'Pendiente' }}
                                </dd>
                            </div>
                        </div>
                    </dl>

                    {{-- Botón Liberar --}}
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        @can('release', $workOrder)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Verifique todos los items antes de liberar</span>
                                </div>
                                <form id="release-form" method="POST" action="{{ route('work_orders.release', $workOrder) }}">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" 
                                            id="release-button" 
                                            disabled 
                                            onclick="return confirm('¿Está seguro de liberar esta orden y marcarla como enviada? Esta acción no se puede deshacer.')"
                                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold text-sm rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ __('Liberar/Enviar Orden') }}
                                    </button>
                                </form>
                            </div>
                        @elseif($workOrder->status === 'Enviado' || $workOrder->completed_at !== null)
                            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-r-lg">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="ml-3 text-sm font-medium text-green-800 dark:text-green-300">
                                        Orden ya enviada/completada.
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded-r-lg">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="ml-3 text-sm font-medium text-yellow-800 dark:text-yellow-300">
                                        @if ($workOrder->status === 'Pendiente Escaneo')
                                            Orden pendiente de escaneo (No se puede liberar).
                                        @elseif ($workOrder->status === 'Enviado')
                                            Orden en proceso de envío.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- Panel de Control RFID --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg shadow-xl border-2 border-blue-500 dark:border-blue-600 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <div>
                                <h3 class="text-lg font-bold text-white">Control de Lector RFID</h3>
                                <p class="text-sm text-blue-100">Sistema de verificación de herramientas</p>
                            </div>
                        </div>
                        <div id="rfidstatus" class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg">
                            <span class="text-sm font-semibold text-white">Lector: Desconocido</span>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Botones de control --}}
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button" 
                                id="connect-rfid-button" 
                                class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Conectar Lector
                        </button>

                        <button type="button" 
                                id="disconnect-rfid-button" 
                                disabled 
                                class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            Desconectar
                        </button>

                        <button type="button" 
                                id="verify-rfid-button" 
                                disabled 
                                class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold text-sm rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Verificar Items (Iniciar Escaneo)
                        </button>

                        <div id="rfid-feedback" class="text-sm font-medium"></div>
                    </div>

                    {{-- Consola de Eventos --}}
                    <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-semibold text-gray-100 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Consola de Eventos RFID
                            </h4>
                            <button type="button" 
                                    id="clear-console-button" 
                                    class="inline-flex items-center px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-white text-xs font-medium rounded transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Limpiar
                            </button>
                        </div>
                        <div id="page-rfid-console" class="h-40 overflow-y-auto p-3 bg-gray-800 dark:bg-gray-950 rounded border border-gray-700 text-xs font-mono text-gray-100 space-y-1">
                            {{-- Los mensajes de log se añadirán aquí por JavaScript --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de Productos Asociados --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <h3 class="text-lg font-semibold text-white">Productos Asociados</h3>
                        </div>
                        <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-bold text-white">
                            {{ $workOrder->productInstances->count() }} items
                        </span>
                    </div>
                </div>

                {{-- Vista Desktop: Tabla --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-16">
                                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                        <span>EPC</span>
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
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>Registrada</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="scanned-items-list" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($workOrder->productInstances as $instance)
                                <tr data-epc="{{ $instance->epc }}" class="instance-item border-l-4 border-transparent hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <td class="px-4 py-4 whitespace-nowrap text-center verification-status">
                                        <svg class="h-5 w-5 text-gray-400 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9.772a4 4 0 105.544 5.544M12 12a4 4 0 00-5.544-5.544" />
                                        </svg>
                                    </td>
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
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                            {{ $instance->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $instance->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-6 mb-4">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-2">No hay items asociados</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">No se han registrado productos en esta orden</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Vista Móvil: Cards --}}
                <div class="md:hidden p-4 space-y-3">
                    @forelse ($workOrder->productInstances as $instance)
                        <div data-epc="{{ $instance->epc }}" class="instance-item bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border-l-4 border-transparent">
                            <div class="flex items-start justify-between mb-3">
                                <div class="verification-status">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9.772a4 4 0 105.544 5.544M12 12a4 4 0 00-5.544-5.544" />
                                    </svg>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                    {{ $instance->status }}
                                </span>
                            </div>
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
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Registrada</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $instance->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-6 mb-4 inline-flex">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">No hay items</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Sin productos asociados</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Footer --}}
            <x-industrial-footer>
                Sistema de verificación RFID - Trazabilidad y control de herramientas
            </x-industrial-footer>

        </div>
    </div>

    @push('scripts')
    <script>
        // ===== VARIABLES GLOBALES =====
        let globalReaderID = null;
        let globalRfidConnected = false;
        let globalScannedTags = new Set();
        let globalIsReading = false;
        const globalTransports = ["usb", "bluetooth", "serial", "all"];
        let globalCurrentTransportIndex = 0;
        let globalReadTimer = null;
        const globalReadDuration = 5000;

        let connectButton, disconnectButton, verifyButton, releaseButton, rfidStatusDiv, pageConsole, clearConsoleButton, instanceItems;
        const verifyUrl = "{{ route('work_orders.verify_rfid', $workOrder) }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // ===== FUNCIONES DE CONSOLA =====
        function appendToPageConsole(message, type = "info") {
            if (!pageConsole) return;
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            const logEntry = document.createElement('div');
            logEntry.textContent = `[${timeString}] ${message}`;

            const colors = {
                error: "#ff7b7b",
                success: "#7bff7b",
                warning: "#ffff7b",
                eb_event: "#7bc0ff",
                info: "#f0f0f0"
            };
            logEntry.style.color = colors[type] || colors.info;

            pageConsole.appendChild(logEntry);
            pageConsole.scrollTop = pageConsole.scrollHeight;
        }

        // ===== CALLBACKS DE ENTERPRISE BROWSER =====
        window.EnumRfidCallback = function(rfidArray) {
            appendToPageConsole(`EB: EnumRfidCallback ejecutado. Lectores: ${rfidArray ? rfidArray.length : 'ninguno'}`, "eb_event");
            if (!rfidArray || rfidArray.length === 0) {
                appendToPageConsole(`⚠️ No se encontraron lectores por ${globalTransports[globalCurrentTransportIndex]}. Probando siguiente...`, "warning");
                globalCurrentTransportIndex++;
                tryNextTransport();
                return;
            }
            globalReaderID = rfidArray[0][0];
            appendToPageConsole(`🔌 Lector encontrado ID: ${globalReaderID}. Conectando...`);
            try {
                rfid.readerID = globalReaderID;
                rfid.tagEvent = "TagHandlerCallback(%json)";
                rfid.statusEvent = "StatusEventCallback(%json)";
                rfid.connect();
            } catch(e) {
                appendToPageConsole(`❌ Error al intentar configurar/conectar lector ${globalReaderID}: ${e.message}`, "error");
                updateUIReaderStatus(false);
            }
        }

        window.StatusEventCallback = function(eventInfo) {
            appendToPageConsole(`EB: StatusEventCallback ejecutado. Info: ${JSON.stringify(eventInfo)}`, "eb_event");
            const statusMsg = eventInfo?.status?.toLowerCase() || eventInfo?.vendorMessage?.toLowerCase() || "";

            if (statusMsg.includes("connect")) {
                updateUIReaderStatus(true, `Lector ${globalReaderID} conectado.`);
            } else if (statusMsg.includes("disconnect")) {
                updateUIReaderStatus(false, `Lector ${globalReaderID} desconectado.`);
            } else if (statusMsg.includes("error")) {
                appendToPageConsole(`Error de estado del lector: ${statusMsg}`, "error");
            }
        }

        window.TagHandlerCallback = function(tagArray) {
            if (globalIsReading && tagArray && Array.isArray(tagArray.TagData)) {
                tagArray.TagData.forEach(tag => {
                    const detectedEpc = tag.tagID;
                    if (detectedEpc && !globalScannedTags.has(detectedEpc)) {
                        globalScannedTags.add(detectedEpc);
                        appendToPageConsole(`Tag detectado: ${detectedEpc} (Total: ${globalScannedTags.size})`);
                    }
                });
            }
        }

        // ===== FUNCIONES AUXILIARES =====
        function tryNextTransport() {
            if (globalCurrentTransportIndex >= globalTransports.length) {
                appendToPageConsole("❌ No se pudo conectar. No se detectaron lectores RFID.", "error");
                updateUIReaderStatus(false);
                connectButton.disabled = false;
                return;
            }
            const transport = globalTransports[globalCurrentTransportIndex];
            appendToPageConsole(`🔍 Buscando lectores por ${transport}...`);
            if (typeof rfid === 'undefined' || rfid === null) {
                appendToPageConsole("CRÍTICO: El objeto 'rfid' de Enterprise Browser NO está definido.", "error");
                if (connectButton) connectButton.disabled = false;
                return;
            }
            
            try {
                rfid.transport = transport;
                rfid.enumRFIDEvent = "EnumRfidCallback(%s)";
                rfid.enumerate();
            } catch(e) {
                appendToPageConsole(`❌ Error al llamar rfid.enumerate() para ${transport}: ${e.message}`, "error");
                globalCurrentTransportIndex++;
                tryNextTransport();
            }
        }

        function updateUIReaderStatus(isConnected, message = "") {
            globalRfidConnected = isConnected;
            rfidStatusDiv.innerHTML = isConnected
                ? `<span class="text-sm font-semibold"><span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>Conectado (ID: ${globalReaderID || 'N/A'})</span>`
                : `<span class="text-sm font-semibold"><span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-2"></span>Desconectado</span>`;

            connectButton.disabled = isConnected;
            disconnectButton.disabled = !isConnected;
            verifyButton.disabled = !isConnected || globalIsReading;

            if (!isConnected) {
                releaseButton.disabled = true;
                if(message) appendToPageConsole(message, "error");
            } else {
                if(message) appendToPageConsole(message, "success");
            }
        }

        function sendEpcsToBackend(detectedEpcs) {
            appendToPageConsole('Verificando items con el servidor...', "info");
            fetch(verifyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ detected_epcs: detectedEpcs })
            })
            .then(response => {
                appendToPageConsole(`Respuesta del backend, status: ${response.status}`);
                if (!response.ok && response.status !== 422) {
                    throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                appendToPageConsole(`Datos JSON recibidos: ${JSON.stringify(data)}`);
                if (data.errors || data.success === false) {
                    let errorMsg = data.message || 'Error desconocido del servidor.';
                    if (data.errors && data.errors.detected_epcs) { 
                        errorMsg = data.errors.detected_epcs[0]; 
                    }
                    throw new Error(errorMsg);
                }

                let verifiedCount = 0;
                instanceItems.forEach(item => {
                    const epc = item.dataset.epc;
                    const statusIcon = item.querySelector('.verification-status svg');

                    // Resetear clases
                    item.classList.remove('bg-green-100', 'dark:bg-green-900', 'bg-red-100', 'dark:bg-red-900', 'border-l-green-500', 'border-l-red-500');
                    item.classList.add('border-l-transparent');
                    if (statusIcon) {
                        statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9.772a4 4 0 105.544 5.544M12 12a4 4 0 00-5.544-5.544" />';
                        statusIcon.classList.remove('text-green-500', 'text-red-500');
                        statusIcon.classList.add('text-gray-400');
                    }

                    if (data.verified_epcs && data.verified_epcs.includes(epc)) {
                        item.classList.add('bg-green-100', 'dark:bg-green-900', 'border-l-green-500');
                        item.classList.remove('border-l-transparent');
                        if(statusIcon) {
                            statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5" />';
                            statusIcon.classList.remove('text-gray-400');
                            statusIcon.classList.add('text-green-500');
                        }
                        verifiedCount++;
                    } else {
                        item.classList.add('bg-red-100', 'dark:bg-red-900', 'border-l-red-500');
                        item.classList.remove('border-l-transparent');
                        if(statusIcon) {
                            statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
                            statusIcon.classList.remove('text-gray-400');
                            statusIcon.classList.add('text-red-500');
                        }
                    }
                });

                if (data.all_verified) {
                    appendToPageConsole(`✓ Verificación completa (${verifiedCount} items). Puede liberar la orden.`, 'success');
                    releaseButton.disabled = false;
                } else {
                    appendToPageConsole(`⚠ Verificación incompleta. Faltantes: ${data.missing_epcs ? data.missing_epcs.length : '?'}. Inesperados: ${data.unexpected_epcs ? data.unexpected_epcs.length : '0'}.`, 'warning');
                    releaseButton.disabled = true;
                }
            })
            .catch(error => {
                appendToPageConsole(`❌ Error: ${error.message}`, "error");
                releaseButton.disabled = true;
            });
        }

        // ===== INICIALIZACIÓN =====
        document.addEventListener('DOMContentLoaded', function() {
            connectButton = document.getElementById('connect-rfid-button');
            disconnectButton = document.getElementById('disconnect-rfid-button');
            verifyButton = document.getElementById('verify-rfid-button');
            releaseButton = document.getElementById('release-button');
            rfidStatusDiv = document.getElementById('rfidstatus');
            pageConsole = document.getElementById('page-rfid-console');
            clearConsoleButton = document.getElementById('clear-console-button');
            instanceItems = document.querySelectorAll('.instance-item');

            if (!connectButton || !disconnectButton || !verifyButton || !releaseButton || !rfidStatusDiv || !pageConsole || !clearConsoleButton || !csrfToken) {
                console.error("Error CRÍTICO: Faltan elementos DOM esenciales");
                return;
            }
            
            updateUIReaderStatus(false);

            clearConsoleButton.addEventListener('click', function() {
                pageConsole.innerHTML = '';
                appendToPageConsole('Consola limpiada.');
            });

            connectButton.addEventListener('click', function() {
                appendToPageConsole('Iniciando conexión...');
                updateUIReaderStatus(false, 'Intentando conectar...');
                connectButton.disabled = true;
                globalCurrentTransportIndex = 0;
                tryNextTransport();
            });

            disconnectButton.addEventListener('click', function() {
                appendToPageConsole('Desconectando...');
                if (globalRfidConnected) {
                    try {
                        if (globalIsReading) {
                            rfid.stop();
                            globalIsReading = false;
                            clearTimeout(globalReadTimer);
                        }
                        rfid.disconnect();
                        updateUIReaderStatus(false, 'Desconexión solicitada.');
                    } catch (e) {
                        appendToPageConsole('Error al desconectar: ' + e.message, "error");
                        updateUIReaderStatus(false);
                    }
                }
            });

            verifyButton.addEventListener('click', function() {
                if (!globalRfidConnected) {
                    appendToPageConsole('Error: Lector no conectado.', 'error');
                    return;
                }
                if (globalIsReading) {
                    appendToPageConsole('Lectura ya en progreso.', 'warning');
                    return;
                }

                appendToPageConsole('Iniciando escaneo...');
                globalIsReading = true;
                verifyButton.disabled = true;
                releaseButton.disabled = true;
                globalScannedTags.clear();

                instanceItems.forEach(item => {
                    item.classList.remove('bg-green-100', 'dark:bg-green-900', 'bg-red-100', 'dark:bg-red-900', 'border-l-green-500', 'border-l-red-500');
                    item.classList.add('border-l-transparent');
                    const statusIcon = item.querySelector('.verification-status svg');
                    if (statusIcon) {
                        statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9.772a4 4 0 105.544 5.544M12 12a4 4 0 00-5.544-5.544" />';
                        statusIcon.classList.remove('text-green-500', 'text-red-500');
                        statusIcon.classList.add('text-gray-400');
                    }
                });

                try {
                    rfid.beepOnRead = 1;
                    rfid.reportUniqueTags = 1;
                    rfid.performInventory();
                    appendToPageConsole(`📡 Leyendo RFID por ${globalReadDuration/1000} segundos...`);

                    clearTimeout(globalReadTimer);
                    globalReadTimer = setTimeout(() => {
                        appendToPageConsole("Tiempo de lectura finalizado.");
                        try { rfid.stop(); } 
                        catch (stopError) { appendToPageConsole(`Error al detener: ${stopError}`, "error"); }

                        globalIsReading = false;
                        verifyButton.disabled = !globalRfidConnected;

                        const finalDetectedEpcs = Array.from(globalScannedTags);
                        appendToPageConsole(`EPCs detectados: ${finalDetectedEpcs.join(', ')}`);
                        sendEpcsToBackend(finalDetectedEpcs);

                    }, globalReadDuration);

                } catch (e) {
                    appendToPageConsole('Error al iniciar: ' + e.message, "error");
                    globalIsReading = false;
                    verifyButton.disabled = !globalRfidConnected;
                }
            });

            appendToPageConsole('Sistema RFID inicializado.');
        });
    </script>
    @endpush
</x-app-layout>