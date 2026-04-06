<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('containers.show', $container) }}" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100">Modo Escáner</h2>
                    <p class="text-xs text-gray-500">{{ $container->container_number }}</p>
                </div>
            </div>
            <div class="bg-teal-100 text-teal-800 px-3 py-1 rounded-full text-sm font-bold">
                {{ $container->inspectionLabels->count() }} Pendientes
            </div>
        </div>
    </x-slot>

    <div class="py-4" x-data="scannerApp()">
        <div class="max-w-md mx-auto px-4 space-y-6">

            {{-- ZONA DE ESCANEO ACTIVO --}}
            <div class="bg-slate-800 rounded-2xl shadow-xl p-6 text-center border-t-4 border-teal-500" @click="focusScanner()">
                <div class="mb-4">
                    <i class="fas fa-barcode text-6xl text-teal-400 mb-4" :class="{'animate-pulse': isFocused}"></i>
                    <h3 class="text-xl font-bold text-white">Listo para Escanear</h3>
                    <p class="text-slate-400 text-sm mt-1" x-text="isFocused ? 'Dispare el código de barras' : 'Toque aquí para activar escáner'"></p>
                </div>

                {{-- El input real que recibe el disparo de la Zebra DS2208 --}}
                <input type="text" 
                       x-ref="scanInput"
                       x-model="scannedCode"
                       @keydown.enter.prevent="processScan()"
                       @focus="isFocused = true"
                       @blur="isFocused = false"
                       class="opacity-0 absolute -z-10" 
                       autocomplete="off" autofocus>
            </div>

            {{-- FEEDBACK VISUAL (Última etiqueta escaneada) --}}
            <template x-if="lastScanned">
                <div class="bg-green-100 border border-green-400 p-4 rounded-xl shadow-sm flex items-center justify-between animate-fade-in-up">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold text-green-800 text-sm" x-text="lastScanned.code"></p>
                            <p class="text-green-600 text-xs truncate w-48" x-text="lastScanned.item"></p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-green-800 bg-green-200 px-2 py-1 rounded">OK</span>
                </div>
            </template>

        </div>
    </div>

    <script>
        function scannerApp() {
            return {
                isFocused: false,
                scannedCode: '',
                lastScanned: null,
                
                init() {
                    // Mantiene el foco en el input oculto para que el DS2208 siempre escriba ahí
                    this.$watch('isFocused', value => {
                        if(!value) setTimeout(() => this.focusScanner(), 500);
                    });
                },

                focusScanner() {
                    this.$refs.scanInput.focus();
                },

                processScan() {
                    if (this.scannedCode.trim() === '') return;
                    
                    let code = this.scannedCode.trim();
                    
                    // Aquí simulamos el éxito (En el siguiente paso conectaremos esto a Laravel con Axios)
                    this.lastScanned = {
                        code: code,
                        item: 'Validando en sistema...' 
                    };

                    this.scannedCode = ''; // Limpiamos para el siguiente disparo
                }
            }
        }
    </script>
    
    <style>
        .animate-fade-in-up {
            animation: fadeInUp 0.3s ease-out forwards;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</x-app-layout>