<x-app-layout>
    {{-- Encabezado --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalles de la Herramienta') }}: {{ $product->name }}
        </h2>
    </x-slot>

    {{-- Contenido --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">

                    {{-- Mostramos cada campo --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Nombre</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $product->name }}</p>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Descripción</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $product->description ?: 'N/A' }}</p> {{-- Muestra N/A si la descripción es nula --}}
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Código de Barras</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $product->barcode }}</p>
                    </div>


                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Fecha de Creación</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $product->created_at->format('d/m/Y H:i') }}</p> {{-- Formateamos la fecha --}}
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Última Actualización</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $product->updated_at->format('d/m/Y H:i') }}</p> {{-- Formateamos la fecha --}}
                    </div>

                    {{-- Botones de Acción/Navegación --}}
                    <div class="flex items-center gap-4 mt-6">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Volver a la Lista') }}
                        </a>
                        <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Editar') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>