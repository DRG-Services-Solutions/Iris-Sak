<x-app-layout>
    {{-- Encabezado --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Auditoría RFID para Orden:') }} {{ $workOrder->folio }}
            </h2>
            <a href="{{ route('audit.work_orders.list') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                Volver a Lista de Auditorías
            </a>
        </div>
    </x-slot>

    {{-- Contenido Principal --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Sección Detalles de la Orden --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                      <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Información de la Orden a Auditar</h3>
                      <dl class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                          <div><dt class="font-medium text-gray-500">Folio:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $workOrder->folio }}</dd></div>
                          <div><dt class="font-medium text-gray-500">Usuario:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $workOrder->user->name }}</dd></div>
                          <div><dt class="font-medium text-gray-500">Estado:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $workOrder->status }}</dd></div>
                          <div><dt class="font-medium text-gray-500">Proceso:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $workOrder->process }}</dd></div>
                          <div><dt class="font-medium text-gray-500">Estación:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $workOrder->station }}</dd></div>
                          <div><dt class="font-medium text-gray-500">Enviada el:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $workOrder->updated_at->format('d/m/Y H:i') }}</dd></div>
                          {{-- Podrías añadir más detalles si son relevantes para la auditoría --}}
                      </dl>

                      {{-- Sección de Acciones RFID y Finalización de Auditoría --}}
                      <div class="mt-6 flex flex-wrap items-center gap-2 border-t dark:border-gray-700 pt-4">
                            <div id="rfidstatus" class="text-sm font-medium text-gray-500 dark:text-gray-400 basis-full md:basis-auto mb-2 md:mb-0 md:mr-2">Lector: Desconocido</div>

                            <button type="button" id="connect-rfid-button" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-500 active:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Conectar Lector</button>
                            <button type="button" id="disconnect-rfid-button" disabled class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">Desconectar</button>
                            <button type="button" id="verify-rfid-button" disabled class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">Iniciar Lectura RFID</button>
                            <button type="button" id="stop-rfid-inventory-button" disabled class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Detener Lectura y Verificar
                            </button>
                            <div id="rfid-feedback" class="text-sm ml-2 basis-full md:basis-auto mt-2 md:mt-0 flex-grow"></div>

                            {{-- Botón para Finalizar Auditoría (empieza deshabilitado) --}}
                            <div class="ml-0 md:ml-auto mt-2 md:mt-0"> {{-- Ajuste para que no siempre se empuje a la derecha si hay pocos botones --}}
                                <form id="complete-audit-form" method="POST" action="{{ route('audit.work_orders.complete', $workOrder) }}">
                                    @csrf
                                    {{-- Pasaremos los resultados de la verificación vía JS si es necesario, o el backend los recalcula --}}
                                    <x-primary-button type="submit" id="complete-audit-button" disabled onclick="return confirm('¿Estás seguro de finalizar la auditoría para esta orden?')">
                                        {{ __('Finalizar Auditoría') }}
                                    </x-primary-button>
                                </form>
                            </div>
                      </div>
                 </div>
            </div>

            {{-- Lista de Instancias (Productos Asociados) a Auditar --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                      <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Items de la Orden a Verificar</h3>
                       <div class="overflow-x-auto">
                         <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                             <thead class="bg-gray-50 dark:bg-gray-600">
                                 <tr>
                                     <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12">Verif.</th>
                                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">EPC</th>
                                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado Original</th>
                                 </tr>
                             </thead>
                             <tbody id="audit-items-list" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                 @forelse ($workOrder->productInstances as $instance)
                                     <tr data-epc="{{ $instance->epc }}" class="instance-item border-e-4 border-transparent"> {{-- La clase 'instance-item' es usada por el JS --}}
                                         <td class="px-2 py-4 whitespace-nowrap text-center text-sm verification-status">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9.772a4 4 0 105.544 5.544M12 12a4 4 0 00-5.544-5.544" /> {{-- Icono inicial --}}
                                            </svg>
                                         </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $instance->epc }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->product->name }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->status }}</td>
                                     </tr>
                                 @empty
                                     <tr>
                                         <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center italic">No hay items asociados a esta orden para auditar.</td>
                                     </tr>
                                 @endforelse
                             </tbody>
                         </table>
                     </div>
                 </div>
            </div>

            {{-- Consola de Eventos RFID en Página --}}
            <div class="mt-6 p-4 sm:p-8 bg-gray-900 text-white dark:bg-black shadow sm:rounded-lg">
                <h3 class="text-md font-medium text-gray-100 dark:text-gray-200 mb-2">Consola de Eventos RFID:</h3>
                <div id="page-rfid-console" class="h-40 overflow-y-auto p-2 border border-gray-700 rounded bg-gray-800 dark:bg-gray-900 text-xs font-mono space-y-1">
                    {{-- Los mensajes de log se añadirán aquí por JavaScript --}}
                </div>
                <button type="button" id="clear-console-button" class="mt-2 px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600">Limpiar Consola</button>
            </div>
        </div>
    </div>

@push('scripts')
{{-- Scripts de Enterprise Browser (asegúrate que estén en tu <head> o aquí ANTES de este script) --}}
{{-- <script src="/ebapi-modules.js" type="text/javascript" charset="utf-8"></script> --}}
{{-- <script src="/elements.js" type="text/javascript" charset="utf-8"></script> --}}

<script>
    // --- Callbacks Globales para Enterprise Browser ---
    // Estas funciones deben ser accesibles globalmente porque EB las llama por nombre de string.
    // Moveremos la lógica principal a funciones dentro de DOMContentLoaded,
    // y estas funciones globales solo actuarán como puentes o delegarán.

    // Estado global para el script RFID
    const rfidState = {
        readerID: null,
        isConnected: false,
        isReading: false,
        scannedTags: new Set(),
        transports: ["usb", "bluetooth", "serial", "all"], // Orden de preferencia
        currentTransportIndex: 0,
        readTimer: null,
        readDuration: 7000, // Leer por 7 segundos (ajustable)

        // Referencias DOM (se llenarán en DOMContentLoaded)
        connectButton: null,
        disconnectButton: null,
        startInventoryButton : null,
        completeAuditButton: null,
        rfidStatusDiv: null,
        rfidFeedback: null,
        pageConsole: null,
        clearConsoleButton: null,
        instanceItems: null,
        verifyRfidUrl: null,
        csrfToken: null
    };

    // Callback para enumerar lectores
    window.handleRfidEnumGlobal = function(rfidArray) {
        rfidState.pageConsole?.dispatchEvent(new CustomEvent('log', { detail: { message: `EB: EnumRfidCallback ejecutado. Lectores: ${rfidArray ? rfidArray.length : 'ninguno'}`, type: "eb_event" }}));
        if (!rfidArray || rfidArray.length === 0) {
            rfidState.pageConsole?.dispatchEvent(new CustomEvent('log', { detail: { message: `⚠️ No se encontraron lectores por ${rfidState.transports[rfidState.currentTransportIndex]}. Probando siguiente...`, type: "warning" }}));
            rfidState.currentTransportIndex++;
            document.dispatchEvent(new Event('tryNextRfidTransport')); // Disparar evento para reintentar
            return;
        }
        rfidState.readerID = rfidArray[0][0]; // Tomar el primer lector
        rfidState.pageConsole?.dispatchEvent(new CustomEvent('log', { detail: { message: `🔌 Lector encontrado ID: ${rfidState.readerID} (transporte: ${rfidState.transports[rfidState.currentTransportIndex]}). Conectando...`} }));
        try {
            rfid.readerID = rfidState.readerID;
            rfid.tagEvent = "handleTagDataGlobal(%json)";
            rfid.statusEvent = "handleStatusUpdateGlobal(%json)";
            rfid.connect();
        } catch(e) {
            rfidState.pageConsole?.dispatchEvent(new CustomEvent('log', { detail: { message: `❌ Error al intentar configurar/conectar lector ${rfidState.readerID}: ${e.message}`, type: "error" }}));
            document.dispatchEvent(new CustomEvent('updateReaderStatusUI', { detail: { isConnected: false } }));
            if(rfidState.connectButton) rfidState.connectButton.disabled = false;
        }
    }

    // Callback para actualizaciones de estado del lector
    window.handleStatusUpdateGlobal = function(eventInfo) {
        rfidState.pageConsole?.dispatchEvent(new CustomEvent('log', { detail: { message: `EB: StatusEventCallback ejecutado. Info: ${JSON.stringify(eventInfo)}`, type: "eb_event" }}));
        const statusMsg = eventInfo?.status?.toLowerCase() || eventInfo?.vendorMessage?.toLowerCase() || "";
        if (statusMsg.includes("connect")) {
            document.dispatchEvent(new CustomEvent('updateReaderStatusUI', { detail: { isConnected: true, message: `Lector ${rfidState.readerID} conectado.` } }));
        } else if (statusMsg.includes("disconnect")) {
            document.dispatchEvent(new CustomEvent('updateReaderStatusUI', { detail: { isConnected: false, message: `Lector ${rfidState.readerID} desconectado.` } }));
        } else if (statusMsg.includes("error")) {
             rfidState.pageConsole?.dispatchEvent(new CustomEvent('log', { detail: { message: `Error de estado del lector: ${statusMsg}`, type: "error" }}));
        }
    }

    // Callback cuando se lee un tag
    window.handleTagDataGlobal = function(tagArray) {
        if (rfidState.isReading && tagArray && Array.isArray(tagArray.TagData)) {
            tagArray.TagData.forEach(tag => {
                const detectedEpc = tag.tagID; 
                if (detectedEpc) {
                    if (!rfidState.scannedTags.has(detectedEpc)) {
                        rfidState.scannedTags.add(detectedEpc);
                        rfidState.pageConsole?.dispatchEvent(new CustomEvent('log', { detail: { message: `Tag detectado: ${detectedEpc} (Total Set: ${rfidState.scannedTags.size})` }}));
                        if(rfidState.rfidFeedback) rfidState.rfidFeedback.textContent = `${rfidState.scannedTags.size} tags únicos detectados...`;
                    }
                }
            });
        }
    }

    // --- Lógica Principal del Script ---
    document.addEventListener('DOMContentLoaded', function() {
        // Asignar elementos del DOM al estado global
        rfidState.connectButton = document.getElementById('connect-rfid-button');
        rfidState.disconnectButton = document.getElementById('disconnect-rfid-button');
        rfidState.startInventoryButton = document.getElementById('verify-rfid-button'); 
        rfidState.stopInventoryButton = document.getElementById('stop-rfid-inventory-button'); // NUEVO
        rfidState.completeAuditButton = document.getElementById('complete-audit-button');
        rfidState.rfidStatusDiv = document.getElementById('rfidstatus');
        rfidState.rfidFeedback = document.getElementById('rfid-feedback');
        rfidState.pageConsole = document.getElementById('page-rfid-console');
        rfidState.clearConsoleButton = document.getElementById('clear-console-button');
        rfidState.instanceItems = document.querySelectorAll('#audit-items-list .instance-item');
        rfidState.verifyRfidUrl = "{{ route('audit.work_orders.verify_items', $workOrder) }}";
        rfidState.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        rfidState.connectButton = document.getElementById('connect-rfid-button');
        console.log("connectButton:", rfidState.connectButton); // DEBUG

        rfidState.disconnectButton = document.getElementById('disconnect-rfid-button');
        console.log("disconnectButton:", rfidState.disconnectButton); // DEBUG

        rfidState.verifyButton = document.getElementById('verify-rfid-button');
        console.log("verifyButton:", rfidState.verifyButton); // DEBUG

        rfidState.completeAuditButton = document.getElementById('complete-audit-button');
        console.log("completeAuditButton:", rfidState.completeAuditButton); // DEBUG

        rfidState.rfidStatusDiv = document.getElementById('rfidstatus');
        console.log("rfidStatusDiv:", rfidState.rfidStatusDiv); // DEBUG

        rfidState.rfidFeedback = document.getElementById('rfid-feedback');
        console.log("rfidFeedback:", rfidState.rfidFeedback); // DEBUG

        rfidState.pageConsole = document.getElementById('page-rfid-console');
        console.log("pageConsole:", rfidState.pageConsole); // DEBUG

        rfidState.clearConsoleButton = document.getElementById('clear-console-button');
        console.log("clearConsoleButton:", rfidState.clearConsoleButton); // DEBUG

        rfidState.instanceItems = document.querySelectorAll('#audit-items-list .instance-item');
        console.log("instanceItems NodeList:", rfidState.instanceItems); // DEBUG (será una NodeList)

        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        rfidState.csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;
        console.log("CSRF Token encontrado:", rfidState.csrfToken ? 'Sí' : 'No'); // DEBUG



        if (!rfidState.connectButton || !rfidState.disconnectButton || !rfidState.startInventoryButton || !rfidState.stopInventoryButton || !rfidState.completeAuditButton ||
            !rfidState.rfidStatusDiv || !rfidState.rfidFeedback || !rfidState.pageConsole || !rfidState.clearConsoleButton || !rfidState.csrfToken) {
            console.error("Error CRÍTICO: Faltan elementos DOM esenciales para Auditoría RFID o CSRF token en la página.");
            appendToPageConsoleLocal('Error CRÍTICO: Faltan elementos DOM. Revise la consola del navegador.', 'error');
            return;
        }

        updateReaderStatusUILocal(false, "Lector inicialmente desconectado.");

        // --- Función Local para Log en Página ---
        function appendToPageConsoleLocal(message, type = "info") {
            if (!rfidState.pageConsole) return;
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            const logEntry = document.createElement('div');
            logEntry.textContent = `[${timeString}] ${message}`;
            // ... (código de colores para logEntry como antes) ...
            if (type === "error") logEntry.style.color = "#ff7b7b"; else if (type === "success") logEntry.style.color = "#7bff7b"; else if (type === "warning") logEntry.style.color = "#ffff7b"; else if (type === "eb_event") logEntry.style.color = "#7bc0ff"; else logEntry.style.color = "#f0f0f0";
            rfidState.pageConsole.appendChild(logEntry);
            rfidState.pageConsole.scrollTop = rfidState.pageConsole.scrollHeight;
        }
        // Dispatcher para los logs desde los callbacks globales
        rfidState.pageConsole.addEventListener('log', function(e) { appendToPageConsoleLocal(e.detail.message, e.detail.type); });


        rfidState.clearConsoleButton.addEventListener('click', function() {
            rfidState.pageConsole.innerHTML = '';
            appendToPageConsoleLocal('Consola limpiada por el usuario.');
        });


        // --- Función Local para Actualizar UI del Estado del Lector ---
        function updateReaderStatusUILocal(isConnected, message = "") {
            rfidState.isConnected = isConnected;
            rfidState.rfidStatusDiv.innerHTML = isConnected
                ? `<span class="text-green-500 font-semibold">Lector: CONECTADO (ID: ${rfidState.readerID || 'N/A'})</span>`
                : `<span class="text-red-500 font-semibold">Lector: DESCONECTADO</span>`;

            rfidState.connectButton.disabled = isConnected;
            rfidState.disconnectButton.disabled = !isConnected;
            rfidState.startInventoryButton.disabled = !isConnected || rfidState.isReading; // Habilitar solo si conectado Y NO leyendo
            rfidState.stopInventoryButton.disabled = !isConnected || !rfidState.isReading; // Habilitar solo si conectado Y SÍ leyendo
            if (!isConnected) {
                rfidState.completeAuditButton.disabled = true;
                if(rfidState.isReading) { // Si se desconectó mientras leía
                    rfidState.isReading = false; // Marcar que ya no está leyendo
                    clearTimeout(rfidState.readTimer); // Limpiar cualquier temporizador
                }

            }
            if(message) appendToPageConsoleLocal(message, isConnected ? "success" : "info");
        }
        // Dispatcher para actualizar UI desde los callbacks globales
        document.addEventListener('updateReaderStatusUI', function(e) { updateReaderStatusUILocal(e.detail.isConnected, e.detail.message); });


        // --- Lógica de Conexión Adaptada ---
        function tryNextTransportLocal() {
            if (rfidState.currentTransportIndex >= rfidState.transports.length) {
                appendToPageConsoleLocal("❌ No se pudo conectar. No se detectaron lectores RFID.", "error");
                updateReaderStatusUILocal(false);
                rfidState.connectButton.disabled = false;
                return;
            }
            const transport = rfidState.transports[rfidState.currentTransportIndex];
            appendToPageConsoleLocal(`🔍 Buscando lectores por ${transport}...`);
            try {
                 rfid.transport = transport;
                 rfid.enumRFIDEvent = "handleRfidEnumGlobal(%s)"; // Llama a la función global
                 rfid.enumerate();
            } catch(e) {
                 appendToPageConsoleLocal(`❌ Error al enumerar por ${transport}: ${e.message}`, "error");
                 rfidState.currentTransportIndex++;
                 tryNextTransportLocal();
            }
        }

        rfidState.connectButton.addEventListener('click', function() {
            appendToPageConsoleLocal('Botón Conectar presionado.');
            updateReaderStatusUILocal(false, 'Intentando conectar...');
            rfidState.connectButton.disabled = true;
            rfidState.currentTransportIndex = 0;
            tryNextTransportLocal();
        });

        rfidState.disconnectButton.addEventListener('click', function() {
            appendToPageConsoleLocal('Botón Desconectar presionado.');
            if (rfidState.isConnected) {
                try {
                    if (rfidState.isReading) {
                        rfid.stop();
                        rfidState.isReading = false;
                        clearTimeout(rfidState.readTimer);
                        appendToPageConsoleLocal('Lectura detenida antes de desconectar.');
                    }
                    rfid.disconnect();
                    appendToPageConsoleLocal('Comando rfid.disconnect() enviado.');
                    // El statusEvent debería actualizar rfidConnected y la UI
                    // Forzamos para feedback inmediato
                    updateReaderStatusUILocal(false, 'Desconexión solicitada.');
                } catch (e) {
                    appendToPageConsoleLocal('Error al intentar desconectar: ' + e.message, "error");
                    updateReaderStatusUILocal(false); // Forzar estado
                }
            } else {
                appendToPageConsoleLocal('Lector ya desconectado.', 'warning');
            }
        });

        // --- Lógica para "Iniciar Auditoría Items (RFID)" ---
        rfidState.startInventoryButton .addEventListener('click', function() {
            if (!rfidState.isConnected) {
                appendToPageConsoleLocal('Error: Lector no conectado. Conecte primero.', 'error');
                return;
            }
            if (rfidState.isReading) {
                appendToPageConsoleLocal('Lectura ya en progreso.', 'warning');
                return;
            }

            appendToPageConsoleLocal('Iniciando auditoría RFID...');
            rfidState.isReading = true;
            rfidState.startInventoryButton.disabled = true;
            rfidState.stopInventoryButton.disabled = false; 
            rfidState.completeAuditButton.disabled = true;
            rfidState.scannedTags.clear();
            rfidState.rfidFeedback.textContent = '📡 Iniciando lectura RFID continua...';

            rfidState.instanceItems.forEach(item => {
                item.classList.remove('bg-green-100', 'dark:bg-green-900', 'bg-red-100', 'dark:bg-red-900', 'border-l-green-500', 'border-l-red-500');
                item.classList.add('border-l-transparent');
                const statusIcon = item.querySelector('.verification-status svg');
                 if (statusIcon) {
                     statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9.772a4 4 0 105.544 5.544M12 12a4 4 0 00-5.544-5.544" />';
                     statusIcon.classList.remove('text-green-500', 'text-red-500');
                     statusIcon.classList.add('text-gray-400');
                 }
            });

            try {
                rfid.tagEvent = "handleTagDataGlobal(%json)"; // Reasegurar callback global
                rfid.statusEvent = "handleStatusUpdateGlobal(%json)"; // Reasegurar callback global
                rfid.beepOnRead = 1;

                appendToPageConsoleLocal("Llamando a rfid.performInventory()...");
                rfid.performInventory();
                

            } catch (e) {
                appendToPageConsoleLocal('Error al iniciar lectura RFID: ' + e.message, "error");
                rfidState.isReading = false;
                rfidState.startInventoryButton.disabled = !rfidState.isConnected;
                rfidState.stopInventoryButton.disabled = true;

            }
        }); // Fin listener startInventoryButton

        rfidState.stopInventoryButton.addEventListener('click', function() {
            if (!rfidState.isReading) {
                appendToPageConsoleLocal('No hay lectura activa para detener.', 'warning');
                return;
            }
            appendToPageConsoleLocal("Botón Detener Lectura presionado. Deteniendo RFID...");
            try {
                rfid.stop();
                appendToPageConsoleLocal("Llamada a rfid.stop() realizada.");
            } catch (stopError) {
                appendToPageConsoleLocal(`Error al llamar rfid.stop(): ${stopError}`, "error");
            }

            rfidState.isReading = false;
            rfidState.startInventoryButton.disabled = !rfidState.isConnected; // Habilitar Iniciar si está conectado
            rfidState.stopInventoryButton.disabled = true; // Deshabilitar este botón

            const finalDetectedEpcs = Array.from(rfidState.scannedTags);
            appendToPageConsoleLocal(`EPCs finales detectados para enviar: ${finalDetectedEpcs.length > 0 ? finalDetectedEpcs.join(', ') : 'Ninguno'}`);
            sendEpcsToBackendForAuditLocal(finalDetectedEpcs); // Llama a la función fetch para verificar
        }); //Fin listener stopInventoryButton
        


        // --- Función para Enviar al Backend (local a este scope) ---
        function sendEpcsToBackendForAuditLocal(detectedEpcs) {
            rfidState.rfidFeedback.textContent = 'Verificando items con el servidor...';
            fetch(rfidState.verifyRfidUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': rfidState.csrfToken
                },
                body: JSON.stringify({ detected_epcs: detectedEpcs })
            })
            .then(response => { 
                appendToPageConsoleLocal(`Respuesta del backend (verify audit), status: ${response.status}`);
                if (!response.ok && response.status !== 422) { throw new Error(`Error HTTP ${response.status}`); }
                return response.json();
             })
            .then(data => { 
                appendToPageConsoleLocal(`Datos JSON recibidos del backend (verify audit): ${JSON.stringify(data)}`);
                if (data.errors || data.success === false) {
                    let errorMsg = data.message || 'Error desconocido del servidor.';
                    if (data.errors && data.errors.detected_epcs) { errorMsg = data.errors.detected_epcs[0]; }
                    throw new Error(errorMsg);
                }
                let verifiedCount = 0;
                rfidState.instanceItems.forEach(item => {
                    const epc = item.dataset.epc;
                    const statusIcon = item.querySelector('.verification-status svg');
                    // Resetear estilos
                    item.classList.remove('bg-green-100', 'dark:bg-green-900', 'bg-red-100', 'dark:bg-red-900', 'border-l-green-500', 'border-l-red-500');
                    item.classList.add('border-l-transparent');
                    if (statusIcon) {
                        statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9.772a4 4 0 105.544 5.544M12 12a4 4 0 00-5.544-5.544" />';
                        statusIcon.classList.remove('text-green-500', 'text-red-500');
                        statusIcon.classList.add('text-gray-400');
                    }

                    if (data.verified_epcs && data.verified_epcs.includes(epc)) {
                        item.classList.add('bg-green-100', 'dark:bg-green-900', 'border-l-green-500');
                        item.classList.remove('border-l-transparent');
                        if(statusIcon) {
                            statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />'; // Check
                            statusIcon.classList.add('text-green-500');
                        }
                        verifiedCount++;
                    } else { // Si no está en verificados, lo marcamos como faltante
                        item.classList.add('bg-red-100', 'dark:bg-red-900', 'border-l-red-500');
                        item.classList.remove('border-l-transparent');
                        if(statusIcon) {
                            statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />'; // Cross
                            statusIcon.classList.add('text-red-500');
                        }
                    }
                });

        if (data.all_expected_items_found) {
            rfidState.rfidFeedback.textContent = `Auditoría: ¡Todos los items esperados (${verifiedCount}) fueron encontrados! Puede finalizar la auditoría.`;
            if (data.unexpected_epcs && data.unexpected_epcs.length > 0) {
                rfidState.rfidFeedback.textContent += ` Se detectaron ${data.unexpected_epcs.length} items inesperados.`;
            }
            rfidState.rfidFeedback.className = 'text-sm ml-4 text-green-600 dark:text-green-400'; // Sigue siendo success
            if (rfidState.completeAuditButton) rfidState.completeAuditButton.disabled = false;
            } else {
            rfidState.rfidFeedback.textContent = `Auditoría: Verificación incompleta. Items esperados faltantes: ${data.missing_epcs ? data.missing_epcs.length : '?'}.`;
            if (data.unexpected_epcs && data.unexpected_epcs.length > 0) {
                rfidState.rfidFeedback.textContent += ` Items inesperados detectados: ${data.unexpected_epcs.length}.`;
            }
            rfidState.rfidFeedback.className = 'text-sm ml-4 text-red-600 dark:text-red-400'; // O 'warning' si prefieres
            if (rfidState.completeAuditButton) rfidState.completeAuditButton.disabled = true;
            }
            })
            .catch(error => {
                appendToPageConsoleLocal(`Error en fetch a backend (verify audit): ${error.message}`, "error");
                rfidState.rfidFeedback.textContent = 'Error en verificación de auditoría: ' + error.message;
                if (rfidState.completeAuditButton) rfidState.completeAuditButton.disabled = true;
            });
        }

        appendToPageConsoleLocal('Script de página Auditoría RFID inicializado.');
        // Puedes decidir si quieres intentar conectar automáticamente al cargar:
        // if (connectButton) connectButton.click();
    }); // Fin DOMContentLoaded
</script>
@endpush
</x-app-layout>