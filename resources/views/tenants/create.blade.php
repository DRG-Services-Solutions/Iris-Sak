{{-- resources/views/tenants/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-700 p-3 rounded-lg shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    Nuevo Cliente
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Registra una nueva empresa o tenant en el sistema</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-xl border border-gray-200 dark:border-gray-700">
                
                {{-- Banner decorativo superior --}}
                <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-700"></div>

                <div class="p-8">
                    <form action="{{ route('tenants.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Campo: Nombre de la Empresa --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nombre de la Empresa / Cliente <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                                       class="block w-full pl-10 pr-3 py-3 border @error('name') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg leading-5 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                       placeholder="Ej: Industrias Acme S.A. de C.V.">
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo: Estado (Activo/Inactivo) --}}
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_active" name="is_active" type="checkbox" checked
                                           class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded cursor-pointer">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_active" class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer">Cuenta Activa</label>
                                    <p class="text-gray-500 dark:text-gray-400 mt-1">Si desmarcas esta opción, los usuarios de esta empresa no podrán acceder al sistema.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Separador --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6"></div>

                        {{-- Botones de Acción --}}
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('tenants.index') }}" 
                               class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-indigo-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Guardar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>