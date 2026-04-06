<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('containers.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div class="bg-gradient-to-br from-teal-600 to-teal-800 p-3 rounded-lg shadow-lg">
                <i class="fas fa-plus text-white text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">Registrar Contenedor</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ingreso de nuevo contenedor al sistema</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <form method="POST" action="{{ route('containers.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- Número de contenedor --}}
                    <div>
                        <label for="container_number" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                            Número de Contenedor
                            <span class="text-xs font-normal text-gray-400 ml-1">(se auto-llena si sube el Packing List)</span>
                        </label>
                        <input type="text" name="container_number" id="container_number" value="{{ old('container_number') }}"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                               placeholder="Ej: MSKU1234567">
                        @error('container_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Proveedor y País --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="supplier" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Proveedor</label>
                            <input type="text" name="supplier" id="supplier" value="{{ old('supplier') }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                   placeholder="Nombre del proveedor">
                        </div>
                        <div>
                            <label for="origin_country" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">País de Origen</label>
                            <input type="text" name="origin_country" id="origin_country" value="{{ old('origin_country') }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                   placeholder="Ej: China">
                        </div>
                    </div>

                    {{-- Cantidad declarada y Estatus de aduana --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="declared_qty" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                                Cantidad Declarada <span class="text-xs font-normal text-gray-400">(se calcula del PL)</span>
                            </label>
                            <input type="number" name="declared_qty" id="declared_qty" value="{{ old('declared_qty', 0) }}" min="0"
                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            @error('declared_qty')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="customs_status" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                                Estatus de Aduana <span class="text-red-500">*</span>
                            </label>
                            <select name="customs_status" id="customs_status"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="pendiente" {{ old('customs_status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_revision" {{ old('customs_status') === 'en_revision' ? 'selected' : '' }}>En revisión</option>
                                <option value="liberado" {{ old('customs_status') === 'liberado' ? 'selected' : '' }}>Liberado</option>
                                <option value="retenido" {{ old('customs_status') === 'retenido' ? 'selected' : '' }}>Retenido</option>
                            </select>
                        </div>
                    </div>

                    {{-- Packing List --}}
                    <div>
                        <label for="packing_list" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                            Packing List <span class="text-xs font-normal text-gray-400">(CSV, XLS, XLSX — máx. 5MB)</span>
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label for="packing_list" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6" id="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Arrastra tu archivo o <span class="font-semibold text-teal-600">haz clic para seleccionar</span></p>
                                    <p class="text-xs text-gray-400 mt-1">Se importarán los artículos automáticamente</p>
                                </div>
                                <input id="packing_list" name="packing_list" type="file" class="hidden" accept=".csv,.xls,.xlsx" />
                            </label>
                        </div>
                        <p id="file-name" class="text-sm text-teal-600 mt-2 hidden"><i class="fas fa-file-alt mr-1"></i><span></span></p>
                        @error('packing_list')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notas --}}
                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Notas</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                  placeholder="Observaciones adicionales...">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('containers.index') }}" class="px-5 py-2.5 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition text-sm font-medium">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-500 transition text-sm font-medium shadow-lg">
                            <i class="fas fa-save mr-1"></i> Registrar Contenedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('packing_list').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const fileNameEl = document.getElementById('file-name');
            if (fileName) {
                fileNameEl.querySelector('span').textContent = fileName;
                fileNameEl.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>
