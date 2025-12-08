<x-app-layout>
    {{-- Encabezado --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Herramienta') }}: {{ $product->name }} {{-- Mostramos el nombre actual --}}
        </h2>
    </x-slot>

    {{-- Contenido --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Formulario --}}
                    {{-- La acción apunta a la ruta 'update', pasando el ID de la herramienta --}}
                    <form method="POST" action="{{ route('products.update', $product) }}" class="mt-6 space-y-6">
                        @csrf {{-- Protección CSRF --}}
                        @method('PUT') {{-- O @method('PATCH') - ¡Importante para indicar que es una actualización! --}}

                        {{-- Campo Nombre --}}
                        <div>
                            <x-input-label for="name" :value="__('Nombre')" />
                            {{-- Usamos old() para mantener el valor si falla la validación, si no, usamos el valor actual de la herramienta --}}
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $product->name)" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        {{-- Campo Descripción --}}
                        <div>
                            <x-input-label for="description" :value="__('Descripción')" />
                            <textarea required id="description" name="description" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full" rows="3">{{ old('description', $product->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                         {{-- Campo Código de Barras --}}
                        <div>
                            <x-input-label for="barcode" :value="__('Código de Barras')" />
                            <x-text-input id="barcode" name="barcode" type="text" class="mt-1 block w-full" :value="old('barcode', $product->barcode)" required autocomplete="barcode" />
                            <x-input-error class="mt-2" :messages="$errors->get('barcode')" />
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Actualizar Herramienta') }}</x-primary-button>

                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>