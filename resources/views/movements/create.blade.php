<x-app-layout>
    {{-- Encabezado --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-blue-700 to-slate-900 p-3 rounded-lg shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        {{ __('Registrar Movimiento') }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Escaneo y gestión de inventario por lotes</p>
                </div>
            </div>
            <a href="{{ route('movements.index') }}" 
               class="hidden md:inline-flex items-center px-4 py-2 bg-slate-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-200 hover:bg-slate-200 dark:hover:bg-gray-600 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al Historial
            </a>
        </div>
    </x-slot>

    {{-- Contenido Principal --}}
    <div class="py-8" x-data="{ 
        type: 'in',
        barcodeInput: '',
        items: [], 
        
        async addItem() {
            if (this.barcodeInput.trim() === '') return;
            
            try {
                let response = await fetch(`/api/products/scan/${this.barcodeInput}`);
                if (!response.ok) throw new Error();
                let product = await response.json();
                
                // Buscar si ya existe en el carrito
                let existing = this.items.find(i => i.product_id === product.id);
                if (existing) {
                    existing.quantity++;
                } else {
                    this.items.unshift({
                        product_id: product.id,
                        name: product.name,
                        barcode: product.barcode,
                        stock: product.stock,
                        quantity: 1
                    });
                }
                this.barcodeInput = ''; 
                this.$nextTick(() => this.$refs.barcodeInput.focus());
            } catch (e) {
                alert('⚠️ Producto no encontrado o error de conexión');
                this.barcodeInput = '';
                this.$nextTick(() => this.$refs.barcodeInput.focus());
            }
        },
        removeItem(index) {
            if (confirm('¿Eliminar este artículo de la lista?')) {
                this.items.splice(index, 1);
            }
        },
        getTotalItems() {
            return this.items.reduce((sum, item) => sum + item.quantity, 0);
        },
        getProjectedStock(item) {
            if (this.type === 'in') return item.stock + item.quantity;
            if (this.type === 'out') return item.stock - item.quantity;
            return item.quantity;
        }
    }">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Panel Superior: Entrada de Escaneo --}}
            <div class="bg-slate-800 p-6 rounded-t-xl border-b-2 border-slate-700 shadow-2xl">
                <div class="flex flex-col lg:flex-row gap-4 items-end">
                    
                    {{-- Campo de Escaneo --}}
                    <div class="flex-1 w-full">
                        <x-input-label class="text-slate-300 uppercase text-xs font-bold mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            Escaneo de Código de Barras
                        </x-input-label>
                        <div class="relative">
                            <x-text-input 
                                x-ref="barcodeInput"
                                x-model="barcodeInput" 
                                @keydown.enter.prevent="addItem()"
                                autofocus
                                placeholder="Escanee o escriba el código de barras..."
                                class="w-full bg-slate-900 border-slate-700 text-white focus:ring-blue-500 focus:border-blue-500 h-14 text-xl font-mono pr-48"
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                              
                                
                            </div>
                        </div>
                    </div>
                    
                    {{-- Selector de Tipo Global --}}
                    <div class="w-full lg:w-56">
                        <x-input-label class="text-slate-300 uppercase text-xs font-bold mb-2">Tipo de Movimiento</x-input-label>
                        <select x-model="type" 
                                class="w-full bg-slate-900 border-slate-700 text-black rounded-lg h-14 font-bold focus:ring-blue-500 focus:border-blue-500">
                            <option value="in">ENTRADA (+)</option>
                            <option value="out">SALIDA (-)</option>
                            <option value="adjustment">AJUSTE</option>
                        </select>
                    </div>
                </div>

                {{-- Contador de Items --}}
                <div x-show="items.length > 0" 
                     x-transition
                     class="mt-4 flex items-center justify-between bg-slate-900/50 rounded-lg px-4 py-2 border border-slate-700">
                    <div class="flex items-center space-x-2 text-slate-400 text-sm">
                        <span class="font-semibold text-white">Items escaneados:</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-white font-bold text-lg" x-text="items.length"></span>
                        <span class="text-white text-sm">|</span>
                        <span class="text-white text-sm">Total unidades:</span>
                        <span class="text-white font-bold text-lg" x-text="getTotalItems()"></span>
                    </div>
                </div>
            </div>

            {{-- Tabla de Artículos (Carrito) --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl border-x border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Artículo</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Stock Actual</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Cantidad</th>

                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider w-20">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(item, index) in items" :key="item.product_id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    {{-- Producto --}}
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase" x-text="item.name"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 font-mono" x-text="item.barcode"></div>
                                    </td>
                                    
                                    {{-- Stock Actual --}}
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-bold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300" x-text="item.stock"></span>
                                    </td>
                                    
                                    {{-- Cantidad Editable --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button type="button" 
                                                    @click="item.quantity = Math.max(1, item.quantity - 1)"
                                                    class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center transition-colors">
                                                <svg class="w-4 h-4 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </button>
                                            
                                            <input type="number" 
                                                   x-model.number="item.quantity" 
                                                   min="1" 
                                                   class="w-20 text-center px-2 py-1.5 bg-transparent border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 rounded-lg text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            
                                            <button type="button" 
                                                    @click="item.quantity++"
                                                    class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center transition-colors">
                                                <svg class="w-4 h-4 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    
                                    
                                    
                                    {{-- Eliminar --}}
                                    <td class="px-6 py-4 text-center">
                                        <button type="button"
                                                @click="removeItem(index)" 
                                                class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors group"
                                                title="Eliminar artículo">
                                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            
                            {{-- Mensaje cuando está vacío --}}
                            <template x-if="items.length === 0">
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Esperando escaneo de artículos...</p>
                                            <p class="text-gray-400 dark:text-gray-500 text-xs">Utilice el lector o escriba el código manualmente</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer de Acción --}}
            <div class="bg-gray-50 dark:bg-gray-900 p-6 rounded-b-xl border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.355l-.015.015V21a14.663 14.663 0 007.309-12.016m-7.309 12.016a14.663 14.663 0 01-7.309-12.016"/>
                    </svg>
                    <span class="text-sm text-gray-600 dark:text-gray-400 font-bold uppercase tracking-widest">
                        Registro Inmutable
                    </span>
                </div>
                
                {{-- Formulario real que se enviará al servidor --}}
                <form method="POST" 
                      action="{{ route('movements.store') }}"
                      @submit.prevent="
                          if (items.length === 0) {
                              alert('⚠️ Debe escanear al menos un artículo antes de procesar');
                              $refs.barcodeInput.focus();
                              return false;
                          }
                          $el.submit();
                      ">
                    @csrf
                    <input type="hidden" name="type" :value="type">
                    {{-- Serializamos el carrito en un campo oculto --}}
                    <input type="hidden" name="payload" :value="JSON.stringify(items)">
                    
                    <div class="flex items-center gap-3">
                        <a href="{{ route('movements.index') }}" 
                           class="px-5 py-3 text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                            Cancelar
                        </a>
                        
                        <button type="submit" 
                                x-show="items.length > 0" 
                                x-transition
                                :disabled="items.length === 0"
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold rounded-lg shadow-lg flex items-center transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            PROCESAR LOTE
                            <span class="ml-2 bg-white/20 px-2 py-0.5 rounded text-xs" x-text="'(' + items.length + ')'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <x-industrial-footer />
        </div>
    </div>
</x-app-layout>