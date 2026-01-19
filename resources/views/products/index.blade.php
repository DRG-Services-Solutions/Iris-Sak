<x-app-layout>
    {{-- Slot para el encabezado de la página --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-slate-700 to-slate-900 p-3 rounded-lg shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                      Catalogo de Productos
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gestión de Activos y Componentes</p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <div class="text-center px-4 py-2 bg-slate-700 rounded-lg">
                    <p class="text-2xl font-bold text-white">{{ $products->total() ?? count($products) }}</p>
                    <p class="text-xs text-gray-300">Total de Articulos</p>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Contenido principal de la página --}}
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Barra de acciones y búsqueda --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                    {{-- Búsqueda --}}
                    <div class="flex-1 max-w-lg">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" 
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent" 
                                   placeholder="Buscar por nombre, código de barras...">
                        </div>
                    </div>
                    
                    {{-- Botones de acción --}}
                    <div class="flex items-center space-x-3">
                        {{-- Boton para exportar (comentado por ahora) --}}
                         {{--
                        <button class="inline-flex items-center px-4 py-2.5 bg-slate-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-200 hover:bg-slate-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Exportar
                             --}}
                        </button>
                        <a href="{{ route('products.create') }}" 
                           class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-slate-700 to-slate-900 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-slate-600 hover:to-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Nuevo Item
                        </a>
                    </div>
                </div>
            </div>

            {{-- Vista DESKTOP: Tabla (oculta en móvil) --}}
<div class="hidden md:block bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gradient-to-r from-slate-700 to-slate-800">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-100 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span>Articulo</span>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-100 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span>Código de Barras</span>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-100 uppercase tracking-wider">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Descripción</span>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-100 uppercase tracking-wider">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                            <span>Acciones</span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($products as $product)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-slate-600 to-slate-800 rounded-lg flex items-center justify-center shadow-md">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">ID: #{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-mono font-medium bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700">
                                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    {{ $product->barcode }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate" title="{{ $product->description }}">
                                {{ $product->description ?? 'Sin descripción' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('products.show', $product) }}" 
                                   class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5"
                                   title="Ver detalles">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                
                                <a href="{{ route('products.edit', $product) }}" 
                                   class="inline-flex items-center px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-medium rounded-md shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5"
                                   title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar este item? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-md shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5"
                                            title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-6 mb-4">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No hay herramientas registradas</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-6">Comienza agregando tu primer item al inventario</p>
                                <a href="{{ route('products.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Agregar Primer Item
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Paginación Desktop --}}
    @if(method_exists($products, 'links'))
        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $products->links() }}
        </div>
    @endif
    </div>

    {{-- Vista MÓVIL: Cards (oculta en desktop) --}}
    <div class="md:hidden space-y-4">
        @forelse ($products as $product)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Header del Card --}}
                <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 h-10 w-10 bg-white/10 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-bold text-white truncate">{{ $product->name }}</h3>
                                <p class="text-xs text-gray-300">ID: #{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contenido del Card --}}
                <div class="p-4 space-y-3">
                    {{-- Código de Barras --}}
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-1">
                            <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Código de Barras</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-medium bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700">
                                {{ $product->barcode }}
                            </span>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-1">
                            <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Descripción</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $product->description ?? 'Sin descripción' }}
                            </p>
                        </div>
                    </div>

                    {{-- Separador --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3"></div>

                    {{-- Botones de Acción --}}
                    <div class="grid grid-cols-3 gap-2">
                        <a href="{{ route('products.show', $product) }}" 
                        class="inline-flex flex-col items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm transition-all duration-200">
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Ver
                        </a>
                        
                        <a href="{{ route('products.edit', $product) }}" 
                        class="inline-flex flex-col items-center justify-center px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-medium rounded-lg shadow-sm transition-all duration-200">
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar
                        </a>
                        
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este item?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex flex-col items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg shadow-sm transition-all duration-200">
                                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            {{-- Estado vacío móvil --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-8">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-6 mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-2">No hay herramientas</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Agrega tu primer item</p>
                    <a href="{{ route('products.create') }}" 
                    class="inline-flex items-center px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium rounded-lg shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nuevo Item
                    </a>
                </div>
            </div>
        @endforelse

        {{-- Paginación móvil --}}
        @if(method_exists($products, 'links'))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <x-industrial-footer />

        </div>
    </div>
</x-app-layout>