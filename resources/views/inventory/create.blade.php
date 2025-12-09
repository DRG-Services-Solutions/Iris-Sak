<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nuevo Conteo de Inventario RFID') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensaje de error --}}
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-6">Configurar Nuevo Conteo</h3>

                    <form method="POST" action="{{ route('inventory.store') }}">
                        @csrf

                        {{-- Tipo de Inventario --}}
                        <div class="mb-6">
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tipo de Inventario <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="type"
                                name="type"
                                required
                                onchange="toggleStationField()"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                            >
                                <option value="">-- Seleccione un tipo --</option>
                                <option value="general" {{ old('type') === 'general' ? 'selected' : '' }}>Inventario General</option>
                                <option value="ciclo" {{ old('type') === 'ciclo' ? 'selected' : '' }}>Conteo Cíclico</option>
                                <option value="estacion" {{ old('type') === 'estacion' ? 'selected' : '' }}>Por Estación</option>
                            </select>
                            @error('type')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            {{-- Descripciones de cada tipo --}}
                            <div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                                <div id="desc-general" class="hidden">
                                    <strong>Inventario General:</strong> Cuenta todos los items activos en el sistema (Check-In, StandBy, En Proceso).
                                </div>
                                <div id="desc-ciclo" class="hidden">
                                    <strong>Conteo Cíclico:</strong> Realiza un conteo periódico de todos los items activos.
                                </div>
                                <div id="desc-estacion" class="hidden">
                                    <strong>Por Estación:</strong> Cuenta solo los items ubicados en una estación específica.
                                </div>
                            </div>
                        </div>

                        {{-- Campo Estación (condicional) --}}
                        <div id="station-field" class="mb-6 hidden">
                            <label for="station" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Estación <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="station"
                                name="station"
                                value="{{ old('station') }}"
                                placeholder="Ej: 01, 02, SALIDA"
                                maxlength="10"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                            >
                            @error('station')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Notas (opcional) --}}
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Notas (Opcional)
                            </label>
                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                maxlength="1000"
                                placeholder="Observaciones o comentarios adicionales..."
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                            >{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Información adicional --}}
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-md">
                            <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">Información Importante</h4>
                            <ul class="text-sm text-blue-700 dark:text-blue-300 list-disc list-inside space-y-1">
                                <li>Se generará un folio único automáticamente (INV-XXXXX)</li>
                                <li>El conteo iniciará inmediatamente y lo dirigirá a la pantalla de escaneo RFID</li>
                                <li>Podrá pausar y continuar el escaneo en cualquier momento</li>
                                <li>Al finalizar, se generará un reporte con las discrepancias encontradas</li>
                            </ul>
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-between">
                            <a href="{{ route('inventory.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Iniciar Conteo RFID
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleStationField() {
            const typeSelect = document.getElementById('type');
            const stationField = document.getElementById('station-field');
            const stationInput = document.getElementById('station');

            // Ocultar todas las descripciones
            document.getElementById('desc-general').classList.add('hidden');
            document.getElementById('desc-ciclo').classList.add('hidden');
            document.getElementById('desc-estacion').classList.add('hidden');

            // Mostrar descripción del tipo seleccionado
            if (typeSelect.value) {
                document.getElementById('desc-' + typeSelect.value).classList.remove('hidden');
            }

            // Mostrar/ocultar campo de estación
            if (typeSelect.value === 'estacion') {
                stationField.classList.remove('hidden');
                stationInput.required = true;
            } else {
                stationField.classList.add('hidden');
                stationInput.required = false;
                stationInput.value = '';
            }
        }

        // Ejecutar al cargar la página por si hay un valor previo (old input)
        document.addEventListener('DOMContentLoaded', function() {
            toggleStationField();
        });
    </script>
</x-app-layout>
