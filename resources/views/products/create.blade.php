<x-app-layout>
    {{-- Encabezado --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-slate-700 to-slate-900 p-3 rounded-lg shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        {{ __('Agregar Nuevo Producto') }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Registrar nuevo componente en el inventario</p>
                </div>
            </div>
            <a href="{{ route('products.index') }}" 
               class="hidden md:inline-flex items-center px-4 py-2 bg-slate-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-200 hover:bg-slate-200 dark:hover:bg-gray-600 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al Inventario
            </a>
        </div>
    </x-slot>

    {{-- Contenido --}}
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Mostrar mensajes de éxito --}}
            @if (session('success'))
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 border-l-4 border-green-500 rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tarjeta del Formulario --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
                
                {{-- Header del formulario --}}
                <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4 border-b border-slate-600">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-white">Datos del Item</h3>
                    </div>
                    <p class="text-sm text-gray-300 mt-1">Complete la información</p>
                </div>

                {{-- Formulario --}}
                <form method="POST" action="{{ route('products.store') }}" class="p-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Campo Código de Barras --}}
                        <div class="md:col-span-2">
                            <x-input-label for="barcode" :value="__('Código de Barras')" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300" />
                            <div class="mt-2 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </div>
                                <x-text-input 
                                    id="barcode" 
                                    name="barcode" 
                                    type="text" 
                                    class="pl-10 block w-full border-gray-300 dark:border-gray-600 focus:border-slate-500 focus:ring-slate-500 rounded-lg shadow-sm" 
                                    :value="old('barcode')" 
                                    required 
                                    autofocus 
                                    autocomplete="barcode"
                                    placeholder="Ej: 123456789012" />
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('barcode')" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ingrese el código único de identificación del producto</p>
                        </div>
                        
                        {{-- Campo Nombre --}}
                        <div class="md:col-span-2">
                            <x-input-label for="name" :value="__('Nombre del Item')" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300" />
                            <div class="mt-2 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <x-text-input 
                                    id="name" 
                                    name="name" 
                                    type="text" 
                                    class="pl-10 block w-full border-gray-300 dark:border-gray-600 focus:border-slate-500 focus:ring-slate-500 rounded-lg shadow-sm" 
                                    :value="old('name')" 
                                    required  
                                    autocomplete="name"
                                    placeholder="Ej: Pistón de aluminio 2.5L" />
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nombre descriptivo del componente o herramienta</p>
                        </div>

                        {{-- Campo Descripción --}}
                        <div class="md:col-span-2">
                            <x-input-label for="description" :value="__('Descripción Técnica')" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300" />
                            <div class="mt-2 relative">
                                <div class="absolute top-3 left-3 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                    </svg>
                                </div>
                                <textarea 
                                    required 
                                    id="description" 
                                    name="description" 
                                    class="pl-10 border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-slate-500 dark:focus:border-slate-600 focus:ring-slate-500 dark:focus:ring-slate-600 rounded-lg shadow-sm mt-1 block w-full resize-none" 
                                    rows="4"
                                    placeholder="Descripción detallada del componente, especificaciones técnicas, materiales, etc.">{{ old('description') }}</textarea>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Incluya especificaciones técnicas, materiales y características relevantes</p>
                        </div>

                    </div>

                    {{-- Sección de ayuda --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    <strong>Nota:</strong> Asegúrese de ingresar información precisa. El código de barras debe ser único para cada item del inventario.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Separador --}}
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>

                    {{-- Botones de acción --}}
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Los datos están protegidos</span>
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <a href="{{ route('products.index') }}" 
                               class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 shadow-sm transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                {{ __('Cancelar') }}
                            </a>

                            <button type="submit" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-slate-700 to-slate-900 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-slate-600 hover:to-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Guardar Item') }}
                            </button>
                        </div>
                    </div>
                </form>

            </div>

        <x-industrial-footer />


        </div>
    </div>
</x-app-layout>