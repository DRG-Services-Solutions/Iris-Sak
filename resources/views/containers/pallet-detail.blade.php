<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('containers.pallets', $pallet->container) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div class="bg-gradient-to-br from-slate-700 to-slate-900 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-print text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">Impresión Zebra ZT411</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Formato 4" x 2"</p>
                </div>
            </div>
            <button onclick="window.print()" class="hidden md:inline-flex items-center px-5 py-2.5 bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition text-sm font-bold shadow-md">
                <i class="fas fa-print mr-2"></i> Imprimir Etiqueta
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 flex flex-col items-center">
            
            <p class="text-gray-500 mb-4 text-sm font-medium"><i class="fas fa-info-circle mr-1"></i> Vista previa</p>

            @php
                // Agrupamos las cajas por producto para saber cuántos códigos distintos hay
                $groupedBoxes = $pallet->boxes->groupBy('container_item_id'); 
            @endphp

            {{-- CONTENEDOR FÍSICO DE LA ETIQUETA (4x2 pulgadas exactas) --}}
            <div id="pallet-label" class="bg-white text-black border-2 border-black relative box-border flex flex-col" style="width: 4in; height: 2in; padding: 0.125in; font-family: Arial, Helvetica, sans-serif;">
                
                {{-- Fila Superior: Área de Códigos y Datos --}}
                <div class="flex justify-between items-start w-full h-[1.35in] overflow-hidden">
                    
                    {{-- LADO IZQUIERDO: Códigos de barras apilados (Generados por JS) --}}
                    <div class="w-3/5 flex flex-col items-center justify-start pr-2 pt-1 gap-2">
                        @foreach($groupedBoxes as $itemId => $boxesGroup)
                            @php 
                                $barcodeVal = $boxesGroup->first()->containerItem?->barcode ?? 'SIN-SKU'; 
                            @endphp
                            <div class="flex flex-col items-center w-full">
                                {{-- Usamos una clase en lugar de ID para poder renderizar múltiples SVGs --}}
                                <svg class="barcode-svg w-full max-h-[30px]" data-barcode="{{ $barcodeVal }}"></svg>
                                <p class="font-bold text-[9px] tracking-widest mt-[1px]">{{ $barcodeVal }} <span class="font-normal">({{ $boxesGroup->count() }}cajas)</span></p>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- LADO DERECHO: Datos numéricos y de control --}}
                    <div class="w-2/5 text-right text-[11px] leading-snug pl-2 border-l-2 border-black flex flex-col justify-start pt-1 h-full">
                        
                        {{-- Dato de la Tarima --}}
                        <div class="pb-1 mb-1 border-b border-black border-dotted">
                            <p class="font-bold uppercase text-[9px]">Tarima</p>
                            <p class="font-bold text-[13px]">{{ $pallet->pallet_code }}</p>
                        </div>

                        {{-- Dato del Contenedor --}}
                        <div class="pb-1 mb-1 border-b border-black border-dotted">
                            <p class="font-bold uppercase text-[9px]">Contenedor</p>
                            <p class="font-bold text-[12px] leading-tight truncate">ID:{{ $pallet->container->container_seal_number ?? $pallet->container->container_seal_number }}</p>
                        </div>
                        
                        {{-- Cantidades --}}
                        <div class="pt-0.5">
                            <p class="font-bold text-[12px]">Cajas: <span class="font-normal">{{ $pallet->boxes->count() }}</span></p>
                            <p class="font-bold text-[12px]">Pzas: <span class="font-normal">{{ number_format($pallet->boxes->sum('quantity')) }}</span></p>
                        </div>
                    </div>
                </div>

                {{-- Fila Inferior: Líneas para llenado manual --}}
                <div class="absolute bottom-[0.1in] left-[0.125in] right-[0.125in] text-[11px] font-bold uppercase leading-loose">
                    <div class="flex items-end justify-between mb-0.5">
                        <span>Estatus:</span>
                        <div class="border-b border-black flex-1 ml-2"></div>
                    </div>
                    <div class="flex items-end justify-between">
                        <span>Comentarios:</span>
                        <div class="border-b border-black flex-1 ml-2"></div>
                    </div>
                </div>

            </div>
            
            {{-- Botón de imprimir para móviles --}}
            <button onclick="window.print()" class="mt-6 md:hidden items-center px-8 py-3 bg-slate-800 text-white rounded-lg text-lg font-bold shadow-md w-full">
                <i class="fas fa-print mr-2"></i> Imprimir Etiqueta
            </button>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Buscamos TODOS los elementos SVG que tengan la clase 'barcode-svg'
            const barcodes = document.querySelectorAll('.barcode-svg');
            
            barcodes.forEach(function(svg) {
                // Obtenemos el valor del código de barras desde el atributo data-barcode
                const code = svg.getAttribute('data-barcode');
                
                JsBarcode(svg, code, {
                    format: "CODE128",
                    width: 1.5,       // Barras más delgadas para que quepan bien
                    height: 25,       // Altura más corta para permitir apilamiento
                    displayValue: false, 
                    margin: 0,
                    lineColor: "#000000"
                });
            });
        });
    </script>

    <style>
        @media print {
            @page { 
                size: 4in 2in; 
                margin: 0; 
            }
            body * { visibility: hidden; }
            body, html {
                margin: 0 !important;
                padding: 0 !important;
                background-color: #fff !important;
            }
            #pallet-label, #pallet-label * { visibility: visible; }
            #pallet-label { 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 4in !important; 
                height: 2in !important; 
                margin: 0 !important;
                border: none !important; 
                box-shadow: none !important;
            }
        }
    </style>
    @endpush
</x-app-layout>