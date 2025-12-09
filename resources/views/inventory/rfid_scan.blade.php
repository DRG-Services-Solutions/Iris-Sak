<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Escaneo RFID - Inventario:') }} {{ $inventoryCount->folio }}
            </h2>
            <a href="{{ route('inventory.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                Volver a Inventarios
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Información del Conteo --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Información del Conteo</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div><dt class="font-medium text-gray-500">Folio:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $inventoryCount->folio }}</dd></div>
                        <div><dt class="font-medium text-gray-500">Tipo:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $inventoryCount->readable_type }}</dd></div>
                        <div><dt class="font-medium text-gray-500">Usuario:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $inventoryCount->user->name }}</dd></div>
                        @if($inventoryCount->station)
                            <div><dt class="font-medium text-gray-500">Estación:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $inventoryCount->station }}</dd></div>
                        @endif
                        <div><dt class="font-medium text-gray-500">Items Esperados:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $inventoryCount->expected_count ?? count($expectedInstances) }}</dd></div>
                        <div><dt class="font-medium text-gray-500">Iniciado:</dt><dd class="text-gray-900 dark:text-gray-100">{{ $inventoryCount->started_at->format('d/m/Y H:i') }}</dd></div>
                    </dl>

                    {{-- Controles RFID --}}
                    <div class="mt-6 flex flex-wrap items-center gap-2 border-t dark:border-gray-700 pt-4">
                        <div id="rfidstatus" class="text-sm font-medium text-gray-500 dark:text-gray-400 basis-full md:basis-auto mb-2 md:mb-0 md:mr-2">
                            Lector: Desconectado
                        </div>

                        <button type="button" id="connect-rfid-button" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Conectar Lector
                        </button>
                        <button type="button" id="disconnect-rfid-button" disabled class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                            Desconectar
                        </button>
                        <button type="button" id="start-inventory-button" disabled class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                            Iniciar Lectura RFID
                        </button>
                        <button type="button" id="stop-inventory-button" disabled class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                            Detener y Verificar
                        </button>
                        <div id="rfid-feedback" class="text-sm ml-2 basis-full md:basis-auto mt-2 md:mt-0 flex-grow"></div>

                        {{-- Botones de Acción --}}
                        <div class="ml-0 md:ml-auto mt-2 md:mt-0 flex gap-2">
                            <form method="POST" action="{{ route('inventory.cancel', $inventoryCount) }}" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('¿Estás seguro de cancelar este conteo?')" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Cancelar Conteo
                                </button>
                            </form>
                            <form id="complete-inventory-form" method="POST" action="{{ route('inventory.complete', $inventoryCount) }}">
                                @csrf
                                <input type="hidden" name="detected_epcs" id="detected-epcs-input">
                                <button type="submit" id="complete-inventory-button" disabled onclick="return confirm('¿Estás seguro de completar este conteo de inventario?')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Completar Conteo
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resumen de Verificación --}}
            <div id="verification-summary" class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg hidden">
                <div class="max-w-full">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Resumen de Verificación</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-300" id="verified-count">0</div>
                            <div class="text-sm text-green-700 dark:text-green-200">Items Verificados</div>
                        </div>
                        <div class="p-4 bg-red-50 dark:bg-red-900 rounded-lg">
                            <div class="text-2xl font-bold text-red-600 dark:text-red-300" id="missing-count">0</div>
                            <div class="text-sm text-red-700 dark:text-red-200">Items Faltantes</div>
                        </div>
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-300" id="unexpected-count">0</div>
                            <div class="text-sm text-yellow-700 dark:text-yellow-200">Items Inesperados</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de Items Esperados --}}
            @if(count($expectedInstances) > 0)
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-full">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Items Esperados ({{ count($expectedInstances) }})
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-600">
                                    <tr>
                                        <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12">Verif.</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">EPC</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estación</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="inventory-items-list" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($expectedInstances as $instance)
                                        <tr data-epc="{{ $instance->epc }}" class="instance-item">
                                            <td class="px-2 py-4 whitespace-nowrap text-center text-sm verification-status">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9.772a4 4 0 105.544 5.544M12 12a4 4 0 00-5.544-5.544" />
                                                </svg>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $instance->epc }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->product->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->current_station ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $instance->status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Consola de Eventos RFID --}}
            <div class="mt-6 p-4 sm:p-8 bg-gray-900 text-white dark:bg-black shadow sm:rounded-lg">
                <h3 class="text-md font-medium text-gray-100 dark:text-gray-200 mb-2">Consola de Eventos RFID:</h3>
                <div id="page-rfid-console" class="h-40 overflow-y-auto p-2 border border-gray-700 rounded bg-gray-800 dark:bg-gray-900 text-xs font-mono space-y-1">
                    {{-- Los mensajes se añadirán aquí por JavaScript --}}
                </div>
                <button type="button" id="clear-console-button" class="mt-2 px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600">
                    Limpiar Consola
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Estado global para el script RFID
        const rfidState = {
            readerID: null,
            isConnected: false,
            isReading: false,
            scannedTags: new Set(),
            transports: ["usb", "bluetooth", "serial", "all"],
            currentTransportIndex: 0,
            readTimer: null,
            readDuration: 10000, // 10 segundos

            // Referencias DOM
            connectButton: null,
            disconnectButton: null,
            startInventoryButton: null,
            stopInventoryButton: null,
            completeInventoryButton: null,
            rfidStatusDiv: null,
            rfidFeedback: null,
            pageConsole: null,
            clearConsoleButton: null,
            instanceItems: null,
            verifyRfidUrl: null,
            csrfToken: null
        };

        // Callbacks globales para Enterprise Browser
        window.handleRfidEnumGlobal = function(rfidArray) {
            appendToPageConsole(`EB: Lectores encontrados: ${rfidArray ? rfidArray.length : 0}`, "eb_event");
            if (!rfidArray || rfidArray.length === 0) {
                appendToPageConsole(`⚠️ No se encontraron lectores. Probando siguiente transporte...`, "warning");
                rfidState.currentTransportIndex++;
                tryNextTransport();
                return;
            }
            rfidState.readerID = rfidArray[0][0];
            appendToPageConsole(`🔌 Lector encontrado: ${rfidState.readerID}. Conectando...`);
            try {
                rfid.readerID = rfidState.readerID;
                rfid.tagEvent = "handleTagDataGlobal(%json)";
                rfid.statusEvent = "handleStatusUpdateGlobal(%json)";
                rfid.connect();
            } catch(e) {
                appendToPageConsole(`❌ Error al conectar: ${e.message}`, "error");
                updateReaderStatusUI(false);
                if(rfidState.connectButton) rfidState.connectButton.disabled = false;
            }
        };

        window.handleStatusUpdateGlobal = function(eventInfo) {
            const statusMsg = eventInfo?.status?.toLowerCase() || eventInfo?.vendorMessage?.toLowerCase() || "";
            if (statusMsg.includes("connect")) {
                updateReaderStatusUI(true, `Lector ${rfidState.readerID} conectado.`);
            } else if (statusMsg.includes("disconnect")) {
                updateReaderStatusUI(false, `Lector ${rfidState.readerID} desconectado.`);
            } else if (statusMsg.includes("error")) {
                appendToPageConsole(`Error de estado: ${statusMsg}`, "error");
            }
        };

        window.handleTagDataGlobal = function(tagArray) {
            if (rfidState.isReading && tagArray && Array.isArray(tagArray.TagData)) {
                tagArray.TagData.forEach(tag => {
                    const detectedEpc = tag.tagID;
                    if (detectedEpc && !rfidState.scannedTags.has(detectedEpc)) {
                        rfidState.scannedTags.add(detectedEpc);
                        appendToPageConsole(`✓ Tag detectado: ${detectedEpc} (Total: ${rfidState.scannedTags.size})`);
                        if(rfidState.rfidFeedback) {
                            rfidState.rfidFeedback.textContent = `${rfidState.scannedTags.size} tags únicos detectados...`;
                        }
                    }
                });
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar referencias DOM
            rfidState.connectButton = document.getElementById('connect-rfid-button');
            rfidState.disconnectButton = document.getElementById('disconnect-rfid-button');
            rfidState.startInventoryButton = document.getElementById('start-inventory-button');
            rfidState.stopInventoryButton = document.getElementById('stop-inventory-button');
            rfidState.completeInventoryButton = document.getElementById('complete-inventory-button');
            rfidState.rfidStatusDiv = document.getElementById('rfidstatus');
            rfidState.rfidFeedback = document.getElementById('rfid-feedback');
            rfidState.pageConsole = document.getElementById('page-rfid-console');
            rfidState.clearConsoleButton = document.getElementById('clear-console-button');
            rfidState.instanceItems = document.querySelectorAll('#inventory-items-list .instance-item');
            rfidState.verifyRfidUrl = "{{ route('inventory.verify-rfid', $inventoryCount) }}";
            rfidState.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!rfidState.connectButton || !rfidState.csrfToken) {
                console.error("Error: Faltan elementos DOM esenciales.");
                appendToPageConsole('Error CRÍTICO: Elementos DOM faltantes.', 'error');
                return;
            }

            // Función para agregar mensajes a la consola
            function appendToPageConsole(message, type = "info") {
                if (!rfidState.pageConsole) return;
                const now = new Date();
                const timeString = now.toLocaleTimeString();
                const logEntry = document.createElement('div');
                logEntry.textContent = `[${timeString}] ${message}`;

                if (type === "error") logEntry.style.color = "#ff7b7b";
                else if (type === "success") logEntry.style.color = "#7bff7b";
                else if (type === "warning") logEntry.style.color = "#ffff7b";
                else if (type === "eb_event") logEntry.style.color = "#7bc0ff";
                else logEntry.style.color = "#f0f0f0";

                rfidState.pageConsole.appendChild(logEntry);
                rfidState.pageConsole.scrollTop = rfidState.pageConsole.scrollHeight;
            }

            window.appendToPageConsole = appendToPageConsole;

            // Función para actualizar UI del lector
            function updateReaderStatusUI(isConnected, message = "") {
                rfidState.isConnected = isConnected;
                rfidState.rfidStatusDiv.innerHTML = isConnected
                    ? `<span class="text-green-500 font-semibold">Lector: CONECTADO (${rfidState.readerID})</span>`
                    : `<span class="text-red-500 font-semibold">Lector: DESCONECTADO</span>`;

                rfidState.connectButton.disabled = isConnected;
                rfidState.disconnectButton.disabled = !isConnected;
                rfidState.startInventoryButton.disabled = !isConnected || rfidState.isReading;
                rfidState.stopInventoryButton.disabled = !isConnected || !rfidState.isReading;

                if (!isConnected && rfidState.isReading) {
                    rfidState.isReading = false;
                    clearTimeout(rfidState.readTimer);
                }

                if(message) appendToPageConsole(message, isConnected ? "success" : "info");
            }

            window.updateReaderStatusUI = updateReaderStatusUI;

            // Función para intentar siguiente transporte
            function tryNextTransport() {
                if (rfidState.currentTransportIndex >= rfidState.transports.length) {
                    appendToPageConsole("❌ No se detectaron lectores RFID.", "error");
                    updateReaderStatusUI(false);
                    rfidState.connectButton.disabled = false;
                    return;
                }
                const transport = rfidState.transports[rfidState.currentTransportIndex];
                appendToPageConsole(`🔍 Buscando lectores por ${transport}...`);
                try {
                    rfid.transport = transport;
                    rfid.enumRFIDEvent = "handleRfidEnumGlobal(%s)";
                    rfid.enumerate();
                } catch(e) {
                    appendToPageConsole(`❌ Error al enumerar: ${e.message}`, "error");
                    rfidState.currentTransportIndex++;
                    tryNextTransport();
                }
            }

            window.tryNextTransport = tryNextTransport;

            // Event Listeners
            rfidState.connectButton.addEventListener('click', function() {
                appendToPageConsole('Intentando conectar...');
                updateReaderStatusUI(false);
                rfidState.connectButton.disabled = true;
                rfidState.currentTransportIndex = 0;
                tryNextTransport();
            });

            rfidState.disconnectButton.addEventListener('click', function() {
                if (rfidState.isConnected) {
                    try {
                        if (rfidState.isReading) {
                            rfid.stop();
                            rfidState.isReading = false;
                            clearTimeout(rfidState.readTimer);
                        }
                        rfid.disconnect();
                        appendToPageConsole('Desconexión solicitada.');
                        updateReaderStatusUI(false);
                    } catch (e) {
                        appendToPageConsole('Error al desconectar: ' + e.message, "error");
                    }
                }
            });

            rfidState.startInventoryButton.addEventListener('click', function() {
                rfidState.scannedTags.clear();
                rfidState.isReading = true;
                updateReaderStatusUI(true);
                appendToPageConsole('🔄 Iniciando lectura RFID...', 'success');
                rfidState.rfidFeedback.textContent = 'Leyendo tags...';

                try {
                    rfid.perform();
                } catch(e) {
                    appendToPageConsole('❌ Error al iniciar lectura: ' + e.message, "error");
                    rfidState.isReading = false;
                    updateReaderStatusUI(true);
                }
            });

            rfidState.stopInventoryButton.addEventListener('click', function() {
                try {
                    rfid.stop();
                    rfidState.isReading = false;
                    updateReaderStatusUI(true);
                    appendToPageConsole('⏸ Lectura detenida. Verificando items...', 'warning');

                    // Verificar items
                    verifyScannedItems();
                } catch(e) {
                    appendToPageConsole('Error al detener: ' + e.message, "error");
                }
            });

            rfidState.clearConsoleButton.addEventListener('click', function() {
                rfidState.pageConsole.innerHTML = '';
                appendToPageConsole('Consola limpiada.');
            });

            // Función para verificar items escaneados
            function verifyScannedItems() {
                const detectedEpcs = Array.from(rfidState.scannedTags);

                fetch(rfidState.verifyRfidUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': rfidState.csrfToken
                    },
                    body: JSON.stringify({ detected_epcs: detectedEpcs })
                })
                .then(response => response.json())
                .then(data => {
                    appendToPageConsole(`✓ Verificación completa.`, 'success');
                    appendToPageConsole(`   Verificados: ${data.verified_count}`, 'success');
                    appendToPageConsole(`   Faltantes: ${data.missing_count}`, data.missing_count > 0 ? 'warning' : 'info');
                    appendToPageConsole(`   Inesperados: ${data.unexpected_count}`, data.unexpected_count > 0 ? 'warning' : 'info');

                    // Actualizar resumen
                    document.getElementById('verified-count').textContent = data.verified_count;
                    document.getElementById('missing-count').textContent = data.missing_count;
                    document.getElementById('unexpected-count').textContent = data.unexpected_count;
                    document.getElementById('verification-summary').classList.remove('hidden');

                    // Actualizar tabla
                    data.verified_epcs.forEach(epc => {
                        const row = document.querySelector(`tr[data-epc="${epc}"]`);
                        if (row) {
                            row.querySelector('.verification-status').innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 inline-block" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>`;
                            row.classList.add('bg-green-50', 'dark:bg-green-900');
                        }
                    });

                    data.missing_epcs.forEach(epc => {
                        const row = document.querySelector(`tr[data-epc="${epc}"]`);
                        if (row) {
                            row.querySelector('.verification-status').innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 inline-block" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>`;
                            row.classList.add('bg-red-50', 'dark:bg-red-900');
                        }
                    });

                    // Habilitar botón de completar
                    rfidState.completeInventoryButton.disabled = false;

                    // Guardar EPCs para el formulario
                    document.getElementById('detected-epcs-input').value = JSON.stringify(detectedEpcs);
                })
                .catch(error => {
                    appendToPageConsole('❌ Error en verificación: ' + error, 'error');
                });
            }

            appendToPageConsole('Sistema de inventario RFID iniciado. Conecte el lector para comenzar.');
        });
    </script>
    @endpush
</x-app-layout>
