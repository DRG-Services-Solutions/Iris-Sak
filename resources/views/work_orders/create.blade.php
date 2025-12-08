<x-app-layout>
    {{-- Slot para el encabezado de la página --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Nueva Orden de Trabajo') }}
        </h2>
    </x-slot>

    {{-- Contenido principal --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Inicio de la Orden</h3>

                 
                    <form method="POST" action="{{ route('work_orders.store') }}" class="mt-6 space-y-6">
                        @csrf {{-- Protección contra Cross-Site Request Forgery (Obligatorio) --}}

                        <div>
                            <x-input-label for="process_info" :value="__('Proceso Inicial')" />
                            {{-- Opción 1: Texto simple --}}
                            <p id="process_info" class="mt-1 text-gray-900 dark:text-gray-100 font-semibold">Seleccion/Picking</p>
                            {{-- Opción 2: Similar a un input deshabilitado --}}
                            {{-- <div id="process_info" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400">Seleccion/Picking</div> --}}
                        </div>

                        {{-- Campo Estación Inicial (Ahora Informativo) --}}
                        <div>
                            <x-input-label for="station_info" :value="__('Estación Inicial')" />
                            {{-- Opción 1: Texto simple --}}
                            <p id="station_info" class="mt-1 text-gray-900 dark:text-gray-100 font-semibold">01</p>
                            {{-- Opción 2: Similar a un input deshabilitado --}}
                            {{-- <div id="station_info" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400">01</div> --}}
                        </div>

                        {{-- Botón de envío (sin cambios) --}}
                        <div class="flex items-center gap-4 mt-6"> {{-- Ajusté el margen superior ya que hay menos campos --}}
                            <x-primary-button>
                                {{ __('Crear Órden') }}
                            </x-primary-button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</x-app-layout>