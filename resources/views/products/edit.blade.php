<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-amber-600 to-amber-800 p-3 rounded-lg shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        {{ __('Editar Item') }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Modificar información de: <span class="font-semibold text-amber-600 dark:text-amber-400">{{ $product->name }}</span>
                    </p>
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
            
            {{-- Tarjeta informativa --}}
            <div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl shadow-md overflow-hidden border border-amber-200 dark:border-amber-800">
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="bg-amber-600 p-3 rounded-lg shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-bold text-amber-900 dark:text-amber-100 mb-1">
                                Modo de Edición
                            </h3>
                            <p class="text-sm text-amber-700 dark:text-amber-300">
                                Está modificando la información del item <strong>{{ $product->name }}</strong>. 
                                Los cambios se guardarán inmediatamente al confirmar.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

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
                    <p class="text-sm text-gray-300 mt-1">Actualice la información del componente mecánico</p>
                </div>

                {{-- Formulario --}}
                <form method="POST" action="{{ route('products.update', $product) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

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
                                    :value="old('barcode', $product->barcode)" 
                                    required 
                                    autocomplete="barcode"
                                    placeholder="Ej: 123456789012" />
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('barcode')" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Código único de identificación del producto
                            </p>
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
                                    :value="old('name', $product->name)" 
                                    required  
                                    autofocus
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
                                    placeholder="Descripción detallada del componente, especificaciones técnicas, materiales, etc.">{{ old('description', $product->description) }}</textarea>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Incluya especificaciones técnicas, materiales y características relevantes</p>
                        </div>

                    </div>

                    {{-- Sección de advertencia de cambios --}}
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">
                                    Atención: Modificación de Datos
                                </h4>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Los cambios afectarán todas las referencias a este item</li>
                                        <li>Verifique que el código de barras sea correcto y único</li>
                                        <li>Las instancias RFID mantendrán su relación con este producto</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Información adicional del producto --}}
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                    </svg>
                                    <span class="font-medium">ID:</span>
                                    <span class="ml-1">#{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-medium">Creado:</span>
                                    <span class="ml-1">{{ $product->created_at->format('d/m/Y') }}</span>
                                </div>
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
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 border border-transparent rounded-lg font-semibold text-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                 Actualizar Producto
                            </button>
                        </div>
                    </div>
                </form>

            </div>

            {{-- Tarjeta de ayuda --}}
            <div class="mt-6 bg-slate-50 dark:bg-slate-900/50 rounded-lg p-5 border border-slate-200 dark:border-slate-700">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-2">
                            Consejos para la Edición
                        </h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Al actualizar este item, todas las referencias en órdenes de trabajo y trazabilidad RFID se mantendrán intactas. 
                            Solo la información descriptiva del producto será modificada. Asegúrese de mantener la coherencia en el código de barras.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <x-industrial-footer>
                Sistema de gestión de inventario industrial - Manufactura de motores y componentes mecánicos
            </x-industrial-footer>

        </div>
    </div>
</x-app-layout>