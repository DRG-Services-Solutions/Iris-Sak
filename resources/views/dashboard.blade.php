<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Administracion') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Contenedor Grid Responsivo --}}
            {{-- 1 columna en móvil, 2 en mediano, 3 en grande --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
            {{-- Tarjeta 1: Catálogo de Ordenes (Solo Admins) --}}
                @can('manage-products')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-medium mb-2">Ordenes de Trabajo</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Gestiona las ordenes de trabajo en curso.
                            </p>
                            <a href="{{ route('work_orders.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Ordenes de trabajo
                            </a>
                        </div>
                    </div>
                @endcan
                
                
                
                {{-- Tarjeta 2: Catálogo de Productos (Solo Admins) --}}
                @can('manage-products')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-medium mb-2">Catálogo de Productos</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Gestiona los tipos de productos/herramientas disponibles.
                            </p>
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Ver Catálogo
                            </a>
                        </div>
                    </div>
                @endcan
                

                {{-- Tarjeta 3: Placeholder para Inventario/Instancias --}}
                <!--
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-2">Inventario</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Consulta las existencias (Próximamente).
                        </p>
                        {{-- <a href="#" class="inline-flex ...">Ver Inventario</a> --}}
                    </div>
                </div>
                -->

                 {{-- Tarjeta 4: Placeholder para Logs/Auditoría --}}
                 <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-2">Auditoría de Embarque</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Revisa movimientos listos para envio.
                        </p>
                        <a href="{{ route('audit.work_orders.list') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Auditoría
                        </a>
                    </div>
                </div>
    </div>
</x-app-layout>