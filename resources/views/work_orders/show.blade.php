<x-app-layout>
    {{-- Encabezado --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalles Orden de Trabajo:') }} {{ $workOrder->folio }}
            </h2>
            {{-- Enlace para volver a la lista --}}
            <a href="{{ route('work_orders.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Volver a la Lista
            </a>
        </div>
    </x-slot>

    {{-- Contenido Principal --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Sección Detalles de la Orden --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                      <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Información General</h3>
                      <dl class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                          <div><dt class="font-medium text-gray-500">Folio:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $workOrder->folio }}</dd></div>
                          <div><dt class="font-medium text-gray-500">Usuario:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $workOrder->user->name }}</dd></div>
                          <div><dt class="font-medium text-gray-500">Estado:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $workOrder->status }}</dd></div>
                          <div><dt class="font-medium text-gray-500">Proceso:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $workOrder->process }}</dd></div>
                          <div><dt class="font-medium text-gray-500">Estación:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $workOrder->station }}</dd></div>
                          <div><dt class="font-medium text-gray-500">Iniciada:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $workOrder->started_at ? $workOrder->started_at->format('d/m/Y H:i') : 'N/A' }}</dd></div>
                          <div><dt class="font-medium text-gray-500">Completada:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $workOrder->completed_at ? $workOrder->completed_at->format('d/m/Y H:i') : 'Pendiente' }}</dd></div>
                      </dl>

                      {{-- Sección de Acciones --}}
                      <div class="mt-6 flex items-center gap-4 border-t dark:border-gray-700 pt-4">
                            

                            {{-- Botón Liberar (empieza deshabilitado, se habilita con JS) --}}
                            <div class="ml-auto"> {{-- Empuja a la derecha --}}
                                @can('release', $workOrder)
                                    <form id="release-form" method="POST" action="{{ route('work_orders.release', $workOrder) }}">
                                        @csrf
                                        @method('PUT')
                                        <x-primary-button id="release-button" disabled onclick="return confirm('¿Estás seguro de liberar esta orden y marcarla como enviada? Esta acción no se puede deshacer.')">
                                            {{ __('Liberar/Enviar') }}
                                        </x-primary-button>
                                    </form>
                                @elsecan($workOrder->status === 'Enviado' || $workOrder->completed_at !== null)
                                    <span class="text-sm font-medium text-green-600 dark:text-green-400 italic">Orden ya enviada/completada.</span>
                                @else
                                    <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400 italic">
                                        @if ($workOrder->status === 'Pendiente Escaneo')
                                            Orden pendiente de escaneo (No se puede Liberar).
                                        @elseif ($workOrder->status === 'Enviado')
                                            Orden en proceso de Envio.
                                        @endif
                                    </span>
                                @endcan
                            </div>

                            {{-- Enlace Auditoría --}}
                            @can('view', $workOrder)
                                <a href="{{ route('work_orders.history', $workOrder) }}" class="text-sm text-gray-600 underline dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                    Historial
                                </a>
                            @endcan
                      </div>
                 </div>
            </div>

            <div class="mt-6 p-4 sm:p-8 bg-gray-900 text-white dark:bg-black shadow sm:rounded-lg" style="display:;">
                <h3 class="text-md font-medium text-gray-100 dark:text-gray-200 mb-2">Consola de Eventos RFID:</h3>
            
                <div id="page-rfid-console" class="h-40 overflow-y-auto p-2 border border-gray-700 rounded bg-gray-800 dark:bg-gray-900 text-xs font-mono space-y-1">
                    {{-- Los mensajes de log se añadirán aquí por JavaScript --}}
                </div>
                <button type="button" id="clear-console-button" class="mt-2 px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600">Limpiar Consola</button>
            </div>

            {{-- Lista de Instancias Asociadas --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                      <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Productos Asociados</h3>
                       <div class="overflow-x-auto">
                         <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                             <thead class="bg-gray-50 dark:bg-gray-600">
                                 <tr>
                                     <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12">Verif.</th>
                                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">EPC</th>
                                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado Instancia</th>
                                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registrada</th>
                                 </tr>
                             </thead>
                             <tbody id="scanned-items-list" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                 @forelse ($workOrder->productInstances as $instance)
                                     <tr data-epc="{{ $instance->epc }}" class="instance-item border-l-4 border-transparent">
                                         <td class="px-2 py-4 whitespace-nowrap text-center text-sm verification-status">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9.772a4 4 0 105.544 5.544M12 12a4 4 0 00-5.544-5.544" />
                                            </svg>
                                         </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $instance->epc }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->product->name }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->status }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->created_at->format('d/m/Y H:i') }}</td>
                                     </tr>
                                 @empty
                                     <tr>
                                         {{-- Ajustar colspan al nuevo número de columnas --}}
                                         <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center italic">No hay items asociados a esta orden.</td>
                                     </tr>
                                 @endforelse
                             </tbody>
                         </table>
                     </div>
                 </div>
            </div>
            {{-- Estado del Lector --}}
                            <div id="rfidstatus" class="text-sm font-medium text-gray-500 dark:text-gray-400">Lector: Desconocido</div>

                            {{-- Botón para Conectar --}}
                            <button type="button" id="connect-rfid-button" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-500 active:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Conectar Lector
                            </button>

                            <button type="button" id="disconnect-rfid-button" disabled class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                Desconectar
                            </button>

                            {{-- Botón para Verificar (empieza deshabilitado) --}}
                            <button type="button" id="verify-rfid-button" disabled class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                Verificar Items (Iniciar Escaneo)
                            </button>

                            {{-- Div para feedback de la verificación --}}
                            <div id="rfid-feedback" class="text-sm ml-2"></div>
        </div>
    </div>

    @push('scripts')
{{-- Scripts de Enterprise Browser --}}
-
<script>
    // Estas funciones deben ser globales porque EB las llama por nombre de string
    // desde rfid.enumRFIDEvent, rfid.tagEvent, rfid.statusEvent

    let globalReaderID = null;
    let globalRfidConnected = false;
    let globalScannedTags = new Set();
    let globalIsReading = false;
    const globalTransports = ["usb", "bluetooth", "serial", "all"]; // Preferir USB y Bluetooth
    let globalCurrentTransportIndex = 0;
    let globalReadTimer = null; // Para el timeout de lectura
    const globalReadDuration = 5000; // Ejemplo: 5 segundos

    // Referencias a elementos DOM (se obtendrán en DOMContentLoaded)
    let connectButton, disconnectButton, startInventoryButton, releaseButton, rfidStatusDiv, pageConsole, clearConsoleButton, instanceItems;
    const verifyUrl = "{{ route('work_orders.verify_rfid', $workOrder) }}"; // Se usa en sendEpcsToBackend
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function appendToPageConsole(message, type = "info") {
        if (!pageConsole) return;
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const logEntry = document.createElement('div');
        logEntry.textContent = `[${timeString}] ${message}`;

        if (type === "error") logEntry.style.color = "#ff7b7b";
        else if (type === "success") logEntry.style.color = "#7bff7b";
        else if (type === "warning") logEntry.style.color = "#ffff7b";
        else if (type === "eb_event") logEntry.style.color = "#7bc0ff";
        else logEntry.style.color = "#f0f0f0";

        pageConsole.appendChild(logEntry);
        pageConsole.scrollTop = pageConsole.scrollHeight;
    }

    window.EnumRfidCallback = function(rfidArray) {
        appendToPageConsole(`EB: EnumRfidCallback ejecutado. Lectores: ${rfidArray ? rfidArray.length : 'ninguno'}`, "eb_event");
        if (!rfidArray || rfidArray.length === 0) {
            appendToPageConsole(`⚠️ No se encontraron lectores por ${globalTransports[globalCurrentTransportIndex]}. Probando siguiente...`, "warning");
            globalCurrentTransportIndex++;
            tryNextTransport();
            return;
        }
        globalReaderID = rfidArray[0][0]; // Tomar el primer lector
        appendToPageConsole(`🔌 Lector encontrado ID: ${globalReaderID}. Conectando...`);
        try {
            rfid.readerID = globalReaderID;
            // Asignar los callbacks para los eventos del lector ANTES de conectar
            rfid.tagEvent = "TagHandlerCallback(%json)";
            rfid.statusEvent = "StatusEventCallback(%json)";
            rfid.connect();
            // La confirmación de conexión vendrá por StatusEventCallback
        } catch(e) {
            appendToPageConsole(`❌ Error al intentar configurar/conectar lector ${globalReaderID}: ${e.message}`, "error");
            updateUIReaderStatus(false);
        }
    }

    window.StatusEventCallback = function(eventInfo) {
        appendToPageConsole(`EB: StatusEventCallback ejecutado. Info: ${JSON.stringify(eventInfo)}`, "eb_event");
        const statusMsg = eventInfo?.status?.toLowerCase() || eventInfo?.vendorMessage?.toLowerCase() || "";

        if (statusMsg.includes("connect")) {
            updateUIReaderStatus(true, `Lector ${globalReaderID} conectado.`);
        } else if (statusMsg.includes("disconnect")) {
            updateUIReaderStatus(false, `Lector ${globalReaderID} desconectado.`);
        } else if (statusMsg.includes("error")) {
            appendToPageConsole(`Error de estado del lector: ${statusMsg}`, "error");
        }
        // Puedes añadir más lógica para otros mensajes de estado que sean relevantes
    }

    window.TagHandlerCallback = function(tagArray) {
        // appendToPageConsole(`EB: TagHandlerCallback ejecutado. ${tagArray?.TagData?.length || 0} tags.`, "eb_event");
        if (globalIsReading && tagArray && Array.isArray(tagArray.TagData)) {
            tagArray.TagData.forEach(tag => {
                const detectedEpc = tag.tagID; // Basado en el ejemplo de Zebra, el EPC está en tagID
                if (detectedEpc) {
                    if (!globalScannedTags.has(detectedEpc)) {
                        globalScannedTags.add(detectedEpc);
                        appendToPageConsole(`Tag detectado: ${detectedEpc} (Total Set: ${globalScannedTags.size})`);
                    }
                }
            });
        }
    }

    function tryNextTransport() {
        if (globalCurrentTransportIndex >= globalTransports.length) {
            appendToPageConsole("❌ No se pudo conectar. No se detectaron lectores RFID en ningún transporte.", "error");
            updateUIReaderStatus(false); // Asegura que la UI refleje desconexión
            connectButton.disabled = false; // Permitir reintentar
            return;
        }
        const transport = globalTransports[globalCurrentTransportIndex];
        appendToPageConsole(`🔍 Buscando lectores por ${transport}...`);
        if (typeof rfid === 'undefined' || rfid === null) {
            appendToPageConsole("CRÍTICO: El objeto 'rfid' de Enterprise Browser NO está definido.", "error");
            // Quizás actualizar un estado visual para el usuario
            if (connectButton) connectButton.disabled = false; // Permitir reintentar conectar
            return; // No continuar si el objeto rfid no existe
        }
        
        try {
             rfid.transport = transport;
             rfid.enumRFIDEvent = "EnumRfidCallback(%s)"; // Callback global
             rfid.enumerate();
        } catch(e) {
             appendToPageConsole(`❌ Error al llamar rfid.enumerate() para ${transport}: ${e.message}`, "error");
             globalCurrentTransportIndex++; // Intenta el siguiente aunque falle este
             tryNextTransport();
        }
    }

    function updateUIReaderStatus(isConnected, message = "") {
        globalRfidConnected = isConnected;
        rfidStatusDiv.innerHTML = isConnected
            ? `<span class="text-green-500 font-semibold">Lector: CONECTADO (ID: ${globalReaderID || 'N/A'})</span>`
            : `<span class="text-red-500 font-semibold">Lector: DESCONECTADO</span>`;

        connectButton.disabled = isConnected;
        disconnectButton.disabled = !isConnected;
        verifyButton.disabled = !isConnected || globalIsReading; // Habilitar Verificar solo si conectado Y no leyendo

        if (!isConnected) {
            releaseButton.disabled = true;
            if(message) appendToPageConsole(message, "error");
        } else {
            if(message) appendToPageConsole(message, "success");
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        connectButton = document.getElementById('connect-rfid-button');
        disconnectButton = document.getElementById('disconnect-rfid-button');
        verifyButton = document.getElementById('verify-rfid-button');
        releaseButton = document.getElementById('release-button');
        rfidStatusDiv = document.getElementById('rfidstatus');
        pageConsole = document.getElementById('page-rfid-console');
        clearConsoleButton = document.getElementById('clear-console-button');
        instanceItems = document.querySelectorAll('.instance-item');

        if (!connectButton || !disconnectButton || !verifyButton || !releaseButton || !rfidStatusDiv || !pageConsole || !clearConsoleButton || !csrfToken) {
            console.error("Error CRÍTICO: Faltan elementos DOM esenciales para RFID o CSRF token en la página.");
            appendToPageConsole('Error CRÍTICO: Faltan elementos DOM. Revise la consola del navegador.', 'error');
            return;
        }
        
        updateUIReaderStatus(false, "Lector inicialmente desconectado."); // Estado inicial UI

        clearConsoleButton.addEventListener('click', function() {
            pageConsole.innerHTML = '';
            appendToPageConsole('Consola limpiada por el usuario.');
        });

        connectButton.addEventListener('click', function() {
            appendToPageConsole('Botón Conectar presionado. Iniciando conexión...');
            updateUIReaderStatus(false, 'Intentando conectar...'); // Bloquea botones mientras intenta
            connectButton.disabled = true;
            globalCurrentTransportIndex = 0; // Reiniciar índice de transporte
            tryNextTransport();
        });

        disconnectButton.addEventListener('click', function() {
            appendToPageConsole('Botón Desconectar presionado.');
            if (globalRfidConnected) {
                try {
                    if (globalIsReading) {
                        rfid.stop();
                        globalIsReading = false;
                        clearTimeout(globalReadTimer);
                        appendToPageConsole('Lectura detenida antes de desconectar.');
                    }
                    rfid.disconnect();
                    appendToPageConsole('Comando rfid.disconnect() enviado.');
                    // El statusEvent debería actualizar globalRfidConnected y la UI
                    // Forzamos actualización UI para feedback inmediato
                    updateUIReaderStatus(false, 'Desconexión solicitada.');
                } catch (e) {
                    appendToPageConsole('Error al intentar desconectar: ' + e.message, "error");
                    updateUIReaderStatus(false); // Forzar estado desconectado
                }
            } else {
                appendToPageConsole('Lector ya desconectado.', 'warning');
            }
        });

        verifyButton.addEventListener('click', function() {
            if (!globalRfidConnected) {
                appendToPageConsole('Error: Lector no conectado. Conecte primero.', 'error');
                return;
            }
            if (globalIsReading) {
                appendToPageConsole('Lectura ya en progreso.', 'warning');
                return;
            }

            appendToPageConsole('Botón Verificar presionado. Iniciando escaneo de items...');
            globalIsReading = true;
            verifyButton.disabled = true;
            releaseButton.disabled = true;
            globalScannedTags.clear();

            instanceItems.forEach(item => { /* ... resetear estilos visuales ... */
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
                rfid.beepOnRead = 1; // Configura el beep (1 para sí, 0 para no)
                rfid.reportUniqueTags = 1; // Configura si quieres que EB filtre duplicados (puede ser útil)

                appendToPageConsole("Llamando a rfid.performInventory()...");
                rfid.performInventory(); // Inicia la lectura asíncrona
                appendToPageConsole(`📡 Leyendo RFID por ${globalReadDuration/1000} segundos...`);

                clearTimeout(globalReadTimer);
                globalReadTimer = setTimeout(() => {
                    appendToPageConsole("Tiempo de lectura finalizado. Deteniendo RFID...");
                    try {
                        rfid.stop();
                        appendToPageConsole("Llamada a rfid.stop() realizada.");
                    } catch (stopError) { appendToPageConsole(`Error al llamar rfid.stop(): ${stopError}`, "error"); }

                    globalIsReading = false;
                    verifyButton.disabled = !globalRfidConnected; // Habilitar si sigue conectado

                    const finalDetectedEpcs = Array.from(globalScannedTags);
                    appendToPageConsole(`EPCs finales detectados para enviar: ${finalDetectedEpcs.join(', ')}`);
                    sendEpcsToBackend(finalDetectedEpcs);

                }, globalReadDuration);

            } catch (e) {
                appendToPageConsole('Error al iniciar rfid.performInventory(): ' + e.message, "error");
                globalIsReading = false;
                verifyButton.disabled = !globalRfidConnected;
            }
        }); // Fin listener verifyButton

        function sendEpcsToBackend(detectedEpcs) {
            appendToPageConsole('Verificando items con el servidor de Laravel...', "info");
            fetch(verifyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ detected_epcs: detectedEpcs })
            })
            .then(response => {
                 appendToPageConsole(`Respuesta del backend (verify), status: ${response.status}`);
                 if (!response.ok && response.status !== 422) {
                     throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
                 }
                return response.json();
             })
            .then(data => {
                 appendToPageConsole(`Datos JSON recibidos del backend: ${JSON.stringify(data)}`);
                 if (data.errors || data.success === false) {
                     let errorMsg = data.message || 'Error desconocido del servidor.';
                     if (data.errors && data.errors.detected_epcs) { errorMsg = data.errors.detected_epcs[0]; }
                     throw new Error(errorMsg); // Lanzar para ser atrapado por el .catch
                 }

                 let verifiedCount = 0;
                 instanceItems.forEach(item => {
                     const epc = item.dataset.epc;
                     const statusIcon = item.querySelector('.verification-status svg');

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
                             statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />';
                             statusIcon.classList.add('text-green-500');
                         }
                         verifiedCount++;
                     } else {
                          item.classList.add('bg-red-100', 'dark:bg-red-900', 'border-l-red-500');
                          item.classList.remove('border-l-transparent');
                          if(statusIcon) {
                              statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />';
                              statusIcon.classList.add('text-red-500');
                          }
                     }
                 }); // Fin forEach instanceItems

                 if (data.all_verified) {
                    appendToPageConsole(`Verificación completa (${verifiedCount} items). Puede liberar la orden.`, 'success');
                    releaseButton.disabled = false;
                 } else {
                    appendToPageConsole(`Verificación incompleta. Faltantes: ${data.missing_epcs ? data.missing_epcs.length : '?'}. Inesperados: ${data.unexpected_epcs ? data.unexpected_epcs.length : '0'}.`, 'warning');
                    releaseButton.disabled = true;
                 }
            })
            .catch(error => {
                appendToPageConsole(`Error en fetch a backend o procesamiento JSON: ${error.message}`, "error");
                releaseButton.disabled = true;
            });
        } // Fin sendEpcsToBackend

        appendToPageConsole('Script de página RFID inicializado.');
    }); // Fin DOMContentLoaded
</script>
@endpush
</x-app-layout>
