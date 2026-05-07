<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        Reporte de Almacenamiento
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Lead Time: Tiempo transcurrido desde la recepción hasta el despacho.
                    </p>
                </div>
            </div>
            
            <button onclick="window.print()" class="hidden md:inline-flex items-center px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition text-sm font-medium print:hidden">
                <i class="fas fa-file-pdf mr-2"></i> Exportar
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Cálculo rápido de KPIs en Blade --}}
            @php
                $totalCajas = $reportData->count();
                $promedioDias = $totalCajas > 0 ? round($reportData->avg('dias_almacenamiento'), 1) : 0;
                $maxDias = $totalCajas > 0 ? $reportData->max('dias_almacenamiento') : 0;
            @endphp

            {{-- KPIs Dashboard --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print:hidden">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border-l-4 border-indigo-500 p-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cajas Despachadas</p>
                        <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalCajas) }}</p>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-full">
                        <i class="fas fa-box-open text-indigo-500 text-xl"></i>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border-l-4 border-teal-500 p-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Promedio Almacenado</p>
                        <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $promedioDias }} <span class="text-lg font-medium text-gray-500">días</span></p>
                    </div>
                    <div class="p-3 bg-teal-50 dark:bg-teal-900/30 rounded-full">
                        <i class="fas fa-clock text-teal-500 text-xl"></i>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border-l-4 border-rose-500 p-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Máx. Tiempo en Almacén</p>
                        <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $maxDias }} <span class="text-lg font-medium text-gray-500">días</span></p>
                    </div>
                    <div class="p-3 bg-rose-50 dark:bg-rose-900/30 rounded-full">
                        <i class="fas fa-exclamation-triangle text-rose-500 text-xl"></i>
                    </div>
                </div>
            </div>

            {{-- Tabla de Datos --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-700/50">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                        <i class="fas fa-list-ul text-indigo-500 mr-2"></i> Detalle de Cajas
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Caja LPN</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Artículo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contenedor</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ingreso</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Salida</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Días</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usuario</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse($reportData as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $row->caja_codigo }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900 dark:text-gray-100 truncate max-w-xs" title="{{ $row->articulo }}">{{ $row->articulo }}</p>
                                        <p class="text-xs text-gray-500">{{ $row->sku }}</p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                                            <i class="fas fa-ship mr-1"></i> {{ $row->contenedor }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">
                                        {{ $row->fecha_ingreso }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">
                                        {{ $row->fecha_salida }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        @php
                                            $badgeColor = $row->dias_almacenamiento <= 30 ? 'bg-green-100 text-green-800 border-green-200' : 
                                                         ($row->dias_almacenamiento <= 60 ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 
                                                         'bg-red-100 text-red-800 border-red-200');
                                        @endphp
                                        <span class="inline-flex px-2 py-1 rounded-md text-xs font-bold border {{ $badgeColor }}">
                                            {{ $row->dias_almacenamiento }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-500 dark:text-gray-400 text-xs">
                                        {{ $row->despachado_por }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-3 block"></i>
                                        No hay registros de cajas despachadas en la bitácora.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>

    @push('scripts')
    <style>
        @media print {
            body { background-color: white !important; }
            .dark\:bg-gray-800 { background-color: white !important; color: black !important; border: 1px solid #e5e7eb; }
            .dark\:text-white { color: black !important; }
            .shadow-md { box-shadow: none !important; }
        }
    </style>
    @endpush
</x-app-layout>