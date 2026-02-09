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
                        Panel de Administración
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Sistema de Gestión</p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-2 px-4 py-2 bg-slate-700 rounded-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium text-white">{{ now()->format('d/m/Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Bienvenida --}}
            <div class="mb-8 bg-gradient-to-r from-slate-700 via-slate-800 to-slate-900 rounded-xl shadow-xl overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-2xl md:text-3xl font-bold text-white mb-2">
                                ¡Bienvenido!
                            </h3>
                            <p class="text-gray-300 text-sm md:text-base">
                                Gestiona tu Sistema desde aqui.
                            </p>
                        </div>
                        <div class="hidden md:block">
                            <div class="bg-white/10 backdrop-blur-sm rounded-full p-6">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grid de Módulos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                {{-- Tarjeta 1: Órdenes de Trabajo --}}
                <!--
                @can('manage-products')
                    <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                        <div class="relative">
                            {{-- Banner superior con gradiente --}}
                            <div class="h-2 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700"></div>
                            
                            <div class="p-6">
                                {{-- Icono --}}
                                <div class="flex items-center justify-between mb-4">
                                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-4 rounded-xl shadow-lg">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                    </div>
                                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs font-semibold px-3 py-1 rounded-full">
                                        Activo
                                    </span>
                                </div>

                                {{-- Contenido --}}
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                    Gestion RFID
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 min-h-[3rem]">
                                    Registro e Impresion de Etiquetas.
                                </p>

                                {{-- Botón --}}
                                <a href="{{ route('work_orders.index') }}" 
                                   class="inline-flex items-center justify-center w-full px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                    <span>RFID</span>
                                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endcan
                -->
                
                {{-- Tarjeta 2: Catálogo de Productos --}}
                @can('manage-products')
                    <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                        <div class="relative">
                            {{-- Banner superior con gradiente --}}
                            <div class="h-2 bg-gradient-to-r from-amber-500 via-amber-600 to-amber-700"></div>
                            
                            <div class="p-6">
                                {{-- Icono --}}
                                <div class="flex items-center justify-between mb-4">
                                    <div class="bg-gradient-to-br from-amber-500 to-amber-700 p-4 rounded-xl shadow-lg">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <span class="bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 text-xs font-semibold px-3 py-1 rounded-full">
                                        Activo
                                    </span>
                                </div>

                                {{-- Contenido --}}
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                    Catálogo de Productos
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 min-h-[3rem]">
                                    Administra el catálogo completo de herramientas, componentes y piezas mecánicas del inventario.
                                </p>

                                {{-- Botón --}}
                                <a href="{{ route('products.index') }}" 
                                   class="inline-flex items-center justify-center w-full px-5 py-3 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                    <span>Ver Catálogo</span>
                                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endcan

                {{-- Tarjeta 3: Auditoría de Embarque --}}
            <!--
                <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                    <div class="relative">
                        {{-- Banner superior con gradiente --}}
                        <div class="h-2 bg-gradient-to-r from-emerald-500 via-emerald-600 to-emerald-700"></div>
                        
                        <div class="p-6">
                            {{-- Icono --}}
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 p-4 rounded-xl shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <span class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 text-xs font-semibold px-3 py-1 rounded-full">
                                    Activo
                                </span>
                            </div>

                            {{-- Contenido --}}
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                Auditoría de Embarque
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 min-h-[3rem]">
                                Revisa y valida los movimientos de inventario listos para envío y exportación.
                            </p>

                            {{-- Botón --}}
                            <a href="{{ route('audit.work_orders.list') }}" 
                               class="inline-flex items-center justify-center w-full px-5 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                <span>Iniciar Auditoría</span>
                                <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            -->

                {{-- Tarjeta 4: Inventario --}}
                <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="relative">
                        {{-- Banner superior con gradiente --}}
                        <div class="h-2 bg-gradient-to-r from-green-500 via-green-600 to-green-700"></div>
                        
                        {{-- Badge de estado (opcional) --}}
                        <div class="absolute top-4 right-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                Activo
                            </span>
                        </div>
                        
                        <div class="p-6">
                            {{-- Icono principal --}}
                            <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900 dark:to-green-800 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-boxes text-green-600 dark:text-green-400 text-2xl"></i>
                            </div>

                            {{-- Contenido --}}
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                Control de Inventario
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 min-h-[3rem]">
                                Monitoreo en tiempo real de existencias, entradas y salidas de materiales.
                            </p>

                            {{-- Botón habilitado --}}
                            <a href="{{ route('inventory.index') }}" 
                            class="inline-flex items-center justify-center w-full px-5 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                <i class="fas fa-box-open mr-2"></i>
                                <span>Ver Inventario</span>
                            </a>
                        </div>
                    </div>
                </div>

                

                {{-- Tarjeta 5: Reportes --}}
                <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="relative">
                        {{-- Banner superior con gradiente --}}
                        <div class="h-2 bg-gradient-to-r from-orange-500 via-amber-600 to-yellow-600"></div>
                        
                        {{-- Badge de estado --}}
                        <div class="absolute top-4 right-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                <span class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-1.5 animate-pulse"></span>
                                Activo
                            </span>
                        </div>
                        
                        <div class="p-6">
                            {{-- Icono principal --}}
                            <div class="w-14 h-14 bg-gradient-to-br from-orange-100 to-amber-200 dark:from-orange-900 dark:to-amber-800 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-chart-line text-orange-600 dark:text-orange-400 text-2xl"></i>
                            </div>

                            {{-- Contenido --}}
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                Movimientos
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 min-h-[3rem]">
                                Gestiona Entradas, Salidas y Ajustes de Inventario en el Almacen.
                            </p>

                            {{-- Botón habilitado --}}
                            <a href="{{ route('movements.index') }}" 
                            class="inline-flex items-center justify-center w-full px-5 py-3 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                <i class="fas fa-file-chart-line mr-2"></i>
                                <span>Ver Reportes</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Tarjeta 6: Configuración (Próximamente) --}}
                <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden opacity-75">
                    <div class="relative">
                        {{-- Banner superior con gradiente --}}
                        <div class="h-2 bg-gradient-to-r from-slate-400 via-slate-500 to-slate-600"></div>
                        
                        <div class="p-6">
                            {{-- Icono --}}
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-gradient-to-br from-slate-400 to-slate-600 p-4 rounded-xl shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <span class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-semibold px-3 py-1 rounded-full">
                                    Próximamente
                                </span>
                            </div>

                            {{-- Contenido --}}
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                Configuración
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 min-h-[3rem]">
                                Personaliza parámetros del sistema, usuarios, permisos y preferencias generales.
                            </p>

                            {{-- Botón deshabilitado --}}
                            <button disabled 
                                    class="inline-flex items-center justify-center w-full px-5 py-3 bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-semibold text-sm rounded-lg cursor-not-allowed">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>En Desarrollo</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>



        </div>
    </div>
</x-app-layout>