<x-app-layout>
    {{-- Encabezado --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Procesando Orden de Trabajo:') }} {{ $workOrder->folio }}
        </h2>
    </x-slot>

    

    {{-- Contenido --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Sección de Información de la Orden --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detalles de la Orden</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Usuario: {{ $workOrder->user->name }} |
                        Iniciada: {{ $workOrder->started_at->format('d/m/Y H:i') }} |
                        Proceso: {{ $workOrder->process }} |
                        Estación: {{ $workOrder->station }} |
                        Estado: {{ $workOrder->status }}
                    </p>
                </div>
            </div>

            {{-- Sección de Escaneo --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Escanear Código de Barras</h3>

                    {{-- Aquí iría el formulario/input para el escaneo --}}
                    {{-- Podría ser un simple input que al recibir datos (enter o tab del scanner) envíe una petición AJAX o un formulario --}}
                    <div class="mt-4">
                         <x-input-label for="barcode_input" :value="__('Código de Barras Producto')" />
                         <x-text-input id="barcode_input" name="barcode_input" type="text" class="mt-1 block w-full" autofocus />
                         {{-- Podríamos añadir un botón o usar Javascript para enviar al detectar entrada --}}
                         {{-- <x-primary-button class="mt-2">Registrar Escaneo</x-primary-button> --}}
                    </div>
                    <div id="scan-feedback" class="mt-2 text-sm">
                        {{-- Aquí mostraremos mensajes de éxito/error del escaneo con JS/AJAX --}}
                    </div>
                </div>
            </div>

            {{-- Sección de Items Escaneados --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="w-full">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Lista de Items Escaneados</h3>
                    <div id="scanned-items-list">
                        {{-- Iteramos sobre las instancias cargadas desde el controlador --}}
                        @forelse ($instances as $instance)
                            {{-- Mostramos cada instancia (similar a como lo hace el JS) --}}
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 text-sm dark:text-gray-100">
                                <span class="font-medium">EPC:</span> {{ $instance->epc }} |
                                <span class="font-medium">Producto:</span> {{ $instance->product->name }} | {{-- Accedemos al nombre del producto relacionado --}}
                                <span class="font-medium">Estado:</span> {{ $instance->status }}
                            </div>
                        @empty
                            {{-- Esto solo se muestra si la colección $instances está vacía al cargar la página --}}
                            <p class="text-sm text-gray-500">Aún no se han escaneado items.</p>
                        @endforelse
                        {{-- El JavaScript seguirá añadiendo nuevos items aquí dinámicamente --}}
                    </div>
                </div>
            </div>

             {{-- Sección de Finalización (Botón deshabilitado/oculto por ahora) --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Finalizar Proceso</h3>
                    {{-- Formulario para finalizar la orden (actualizar estado/proceso/estación) --}}
                        <form method="POST" action="{{ route('work_orders.finalize', $workOrder) }}">
                            @csrf
                            @method('PUT') {{-- Usamos PUT (o PATCH) para indicar una actualización --}}
                            {{-- No necesitamos inputs visibles, solo el botón --}}
                            <x-primary-button>
                                {{ __('Finalizar Escaneo') }}
                            </x-primary-button>
                        </form>
                    {{-- <p class="text-sm text-gray-500 mt-2">Al finalizar, la orden pasará al siguiente proceso.</p> --}}
                </div>
            </div>

        </div>
    </div>

    
    @push('scripts')
<script>

    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM Cargado. Iniciando script de escaneo...'); // LOG 1

        const barcodeInput = document.getElementById('barcode_input');
        console.log('Elemento #barcode_input encontrado:', barcodeInput); // LOG 2

        const scannedItemsList = document.getElementById('scanned-items-list');
        const scanFeedback = document.getElementById('scan-feedback');
        const scanUrl = "{{ route('work_orders.scan', $workOrder) }}";
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]'); // Obtener el elemento meta
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null; // Obtener el token si la meta existe
        console.log('CSRF Token:', csrfToken ? 'Encontrado' : 'NO ENCONTRADO!'); // LOG 3

        // === ¡VERIFICACIÓN IMPORTANTE! ===
        if (!barcodeInput) {
            console.error('¡ERROR CRÍTICO! No se pudo encontrar el elemento input con id="barcode_input". El script no puede continuar.');
            scanFeedback.textContent = 'Error interno: No se encontró el campo de código de barras.';
            scanFeedback.classList.add('text-red-600', 'dark:text-red-400');
            return; // Detiene la ejecución aquí si no encontró el input
        }
        if (!csrfToken) {
             console.error('¡ERROR CRÍTICO! No se encontró la meta etiqueta CSRF o su contenido.');
             scanFeedback.textContent = 'Error interno: Falta configuración CSRF.';
             scanFeedback.classList.add('text-red-600', 'dark:text-red-400');
             return; // Detiene la ejecución si falta el token
        }
        // ================================

        scanFeedback.textContent = ''; // Limpiar feedback inicial

        console.log('Adjuntando event listener para "keypress" a:', barcodeInput); // LOG 4
        barcodeInput.addEventListener('keypress', function (event) {
            console.log(`Tecla presionada: ${event.key} (Code: ${event.keyCode})`); // LOG 5 (Ver todas las teclas)

            if (event.key === 'Enter' || event.keyCode === 13) {
                console.log('¡Enter detectado!'); // LOG 6
                event.preventDefault();
                const barcode = barcodeInput.value.trim();
                console.log(`Barcode a enviar: "${barcode}"`); // LOG 7

                if (barcode === '') {
                    console.log('Barcode vacío, no se envía.');
                    return;
                }

                scanFeedback.textContent = 'Procesando...'; // Mensaje mientras espera
                scanFeedback.className = 'mt-2 text-sm text-gray-500';

                 fetch(scanUrl, {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'Accept': 'application/json',
                         'X-CSRF-TOKEN': csrfToken
                     },
                     body: JSON.stringify({ barcode: barcode })
                 })
                 .then(response => {
                      console.log('Respuesta recibida, status:', response.status); 
                      if (!response.ok && response.status !== 422) {
                         throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
                      }
                     return response.json();
                 })
                 .then(data => {
                     console.log('Datos JSON recibidos:', data);
                     if (data.success) {
                // ... (código para mostrar mensaje de éxito en scanFeedback) ...
                console.log('Item añadido a la lista:', data.instance); // <-- Este log SÍ lo ves

                // --->>> INICIO: Fracción que crea y añade la nueva línea <<<---

                console.log('Creando nuevo elemento div...'); // Log A
                const newItem = document.createElement('div');
                newItem.className = 'border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 text-sm';

                console.log('Asignando innerHTML...'); // Log B
                // Verifica que data.instance.product no sea null antes de acceder a name
                const productName = data.instance.product ? data.instance.product.name : 'Producto Desconocido';
                newItem.innerHTML = `
                    <span class="font-medium">EPC:</span> ${data.instance.epc} |
                    <span class="font-medium">Producto:</span> ${productName} |
                    <span class="font-medium">Estado:</span> ${data.instance.status}

                `;
                console.log('innerHTML asignado:', newItem.innerHTML); 

                

                // Añade el nuevo elemento a la lista
                console.log("Intentando añadir newItem al DOM:", newItem); // Log H
                try {
                    scannedItemsList.appendChild(newItem); // Intenta añadir el nuevo div
                    console.log("¡Éxito! Nuevo item añadido al DOM."); // Log I
                } catch (e) {
                    console.error("Error al añadir newItem:", e); // Log J
                }

                // --->>> FIN: Fracción que crea y añade la nueva línea <<<---

                     } else {
                         console.warn('Error en la respuesta del backend:', data.message);
                         scanFeedback.textContent = data.message || 'Error desconocido.';
                         scanFeedback.classList.add('text-red-600', 'dark:text-red-400');
                     }
                 })
                 .catch(error => {
                     console.error('Error en fetch o procesamiento:', error); // LOG 10
                     scanFeedback.textContent = 'Error de conexión o del servidor: ' + error.message;
                     scanFeedback.classList.add('text-red-600', 'dark:text-red-400');
                 })
                 .finally(() => {
                     console.log('Limpiando input y devolviendo foco.'); // LOG 11
                     barcodeInput.value = '';
                     barcodeInput.focus();
                 });
            }
        });

        barcodeInput.focus();
        console.log('Script de escaneo inicializado y listener adjunto.'); // LOG 12

    });
</script>
@endpush
</x-app-layout>

