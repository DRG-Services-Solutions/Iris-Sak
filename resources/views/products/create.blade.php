<x-app-layout>
    {{-- Encabezado --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Añadir Nueva Herramienta') }}
        </h2>
    </x-slot>

    {{-- Contenido --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                        
                
                        {{-- Mostrar mensajes de éxito --}}
                        @if (session('success'))
                            <div class="mb-4 p-4 bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded">
                                {{ session('success') }}
                            </div>
                        @endif


                    {{-- Formulario --}}
                    <form method="POST" action="{{ route('products.store') }}" class="mt-6 space-y-6">
                        @csrf {{-- Directiva obligatoria para protección CSRF --}}

                        {{-- Campo Código de Barras --}}
                        <div>
                            <x-input-label for="barcode" :value="__('Código de Barras')" />
                            <x-text-input id="barcode" name="barcode" type="text" class="mt-1 block w-full" :value="old('barcode')" required autofocus autocomplete="barcode" />
                            <x-input-error class="mt-2" :messages="$errors->get('barcode')" />
                        </div>
                        
                        {{-- Campo Nombre --}}
                        <div>
                            <x-input-label for="name" :value="__('Nombre')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required  autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        {{-- Campo Descripción --}}
                        <div>
                            <x-input-label for="description" :value="__('Descripción')" />
                            {{-- Usamos un textarea para la descripción --}}
                            <textarea required id="description" name="description" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full" rows="3">{{ old('description') }} </textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                       

                        {{-- Botones --}}
                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Guardar Herramienta') }}</x-primary-button>

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