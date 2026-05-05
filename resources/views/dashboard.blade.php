<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-slate-700 to-slate-900 p-3 rounded-lg shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        @role('Super Admin') Panel de Control Global @else Resumen Operativo @endrole
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Bienvenido, {{ Auth::user()->name }} 
                        <span class="mx-2 text-gray-300 dark:text-gray-600">|</span> 
                        <span class="text-indigo-600 dark:text-indigo-400 font-semibold">{{ Auth::user()->getRoleNames()->first() ?? 'Sin Rol' }}</span>
                    </p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-2 px-4 py-2 bg-white dark:bg-slate-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <i class="fas fa-calendar-alt text-gray-500 dark:text-gray-400"></i>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ now()->format('d M, Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @role('Super Admin')
                {{-- ========================================================= --}}
                {{-- 👑 UNIVERSO SUPER ADMIN: MÉTRICAS DE NEGOCIO (SaaS)       --}}
                {{-- ========================================================= --}}

                {{-- KPIs Globales --}}
                {{--  --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- KPI: Ingresos Mensuales (MRR) --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex items-center space-x-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg">
                            <i class="fas fa-brands fa-docker text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Contenedores Procesados</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{$contenedores->count() }} <span class="text-xs font-normal text-emerald-500"></span></p>
                        </div>
                    </div>

                    {{-- KPI: Empresas Activas --}}
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex items-center space-x-4">
                        <div class="p-3 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-lg">
                            <i class="fas fa-building text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Productos Procesados</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $productos->sum('received_qty') }} <span class="text-xs font-normal text-gray-400">Registros</span></p> 
                        </div>
                    </div>
                    

                   

                    {{-- Usuarios--}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex items-center space-x-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 rounded-lg relative">
                            <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-purple-500"></span>
                            </span>
                            <i class="fas fa-server text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Usuarios Totales</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $usuarios->count() }} <span class="text-xs font-normal text-gray-400">activos</span></p>
                        </div>
                    </div>
                </div>

                {{-- Gráficos de Negocio (Placeholders) --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Gráfico: Crecimiento de Ingresos --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Ultimas Tarimas Trabajadas</h3>
                        <div class="space-y-4">
                            @foreach($tarimas as $tarima)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">Tarima ID: {{ $tarima->pallet_code }}</p>
                                        <p class="text-xs text-gray-500">Contenedor: {{ $tarima->container->container_seal_number }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-bold bg-blue-200 dark:bg-blue-800 text-blue-800 dark:text-blue-100 rounded">Último movimiento: {{ $tarima->updated_at->diffForHumans() }}</span>        
                                </div>
                            @endforeach
                            
                        </div>
                    </div>
                  

                    {{-- Distribución de Paquetes --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Eficiencia</h3>
                        <div class="space-y-4">
                            {{-- Plan Enterprise --}}
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-bold text-gray-700 dark:text-gray-300">Surtido</span>
                                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">85%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 85%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1"></p>
                            </div>
                            {{-- Plan Pro --}}
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-bold text-gray-700 dark:text-gray-300">Recepcion</span>
                                    <span class="text-blue-600 dark:text-blue-400 font-bold">58%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: 58%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1"></p>
                            </div>
                            {{-- Plan Básico --}}
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-bold text-gray-700 dark:text-gray-300">Inventarios</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">95%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                    <div class="bg-emerald-600 h-2.5 rounded-full" style="width: 95%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabla: Últimas Suscripciones / Actividad --}}
                <!--
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Empresas Recientes</h3>
                        <a href="{{ route('tenants.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Ver todas</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-white dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Empresa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Plan Asignado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha Registro</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                {{-- Placeholder Row --}}
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">Coca Cola Femsa</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Plan 3 (Enterprise)</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Activa</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Hoy</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">Distribuidora Reyes G</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Plan 1 (Básico)</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Activa</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Ayer</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            -->

            @else
                {{-- ========================================================= --}}
                {{-- 🏢 UNIVERSO CLIENTE: MÉTRICAS OPERATIVAS (INVENTARIO)     --}}
                {{-- ========================================================= --}}

                {{-- KPIs Operativos --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex items-center space-x-4">
                        <div class="p-3 bg-violet-100 dark:bg-violet-900/50 text-violet-600 dark:text-violet-400 rounded-lg">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Mis Empleados</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalUsers ?? 5 }}</p> 
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex items-center space-x-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-lg">
                            <i class="fas fa-boxes text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Catálogo</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalProducts ?? '1,245' }}</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex items-center space-x-4">
                        <div class="p-3 bg-orange-100 dark:bg-orange-900/50 text-orange-600 dark:text-orange-400 rounded-lg">
                            <i class="fas fa-exchange-alt text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Movimientos (Mensual)</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $movimientosMes ?? 0 }}</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex items-center space-x-4">
                        <div class="p-3 bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 rounded-lg relative">
                            <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                            <i class="fas fa-exclamation-triangle text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Bajo</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $alertasStock ?? 5 }} <span class="text-sm font-normal text-red-500">ítems</span></p>
                        </div>
                    </div>
                </div>

                {{-- Resto de la información operativa (Gráficos y Tabla de movimientos) --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Entradas vs Salidas (Últimos 7 días)</h3>
                        <div class="w-full h-64 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex flex-col items-center justify-center">
                            <i class="fas fa-chart-bar text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Gráfico de barras apiladas</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Alertas Críticas</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/10 rounded-lg">
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">Tornillo Hexagonal M8</p>
                                    <p class="text-xs text-gray-500">SKU: TX-8890</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-bold bg-red-200 dark:bg-red-800 text-red-800 dark:text-red-100 rounded">15 unds</span>
                            </div>
                        </div>
                    </div>
                </div>

            @endrole

        </div>
    </div>
</x-app-layout>