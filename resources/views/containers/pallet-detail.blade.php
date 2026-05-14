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
            
            @php
                // Agrupamos las cajas por producto
                $groupedBoxes = $pallet->boxes->groupBy('container_item_id'); 
            @endphp

            {{-- CONTENEDOR DE LA ETIQUETA --}}
            <div id="pallet-label" class="bg-white text-black border-2 border-black relative box-border flex flex-col" style="width: 4in; height: 2in; padding: 2mm; font-family: Arial, Helvetica, sans-serif;">
                
                {{-- Fila Superior: Área de Códigos y Datos --}}
                <div class="flex justify-between items-start w-full overflow-hidden" style="height: 1.35in;">
                    
                    {{-- LADO IZQUIERDO: Códigos de barras (Proporción ajustada) --}}
                    <div class="w-7/12 flex flex-col items-center justify-start pr-2 pt-1 gap-2 box-border">
                        @foreach($groupedBoxes as $itemId => $boxesGroup)
                            @php 
                                $barcodeVal = $boxesGroup->first()->containerItem?->barcode ?? 'SIN-SKU'; 
                            @endphp
                            <div class="flex flex-col items-center w-full">
                                <svg class="barcode-svg w-full max-h-[28px]" data-barcode="{{ $barcodeVal }}"></svg>
                                <div class="flex items-center justify-center space-x-1 mt-[2px]">
                                    <span class="font-bold text-[10px] tracking-widest">{{ $barcodeVal }}</span>
                                    <span class="text-[9px] font-medium">({{ $boxesGroup->count() }}c)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- LADO DERECHO: Datos numéricos --}}
                    <div class="w-5/12 text-right text-[11px] leading-snug pl-2 border-l-2 border-black flex flex-col justify-start pt-1 h-full box-border">
                        
                        {{-- Dato de la Tarima --}}
                        <div class="pb-1 mb-1 border-b border-black border-dotted">
                            <p class="font-bold uppercase text-[9px]">Tarima</p>
                            <p class="font-bold text-[12px] break-all leading-tight">{{ $pallet->pallet_code }}</p>
                        </div>

                        {{-- Dato del Contenedor --}}
                        <div class="pb-1 mb-1 border-b border-black border-dotted">
                            <p class="font-bold uppercase text-[9px]">Contenedor</p>
                            <p class="font-bold text-[12px] leading-tight truncate">{{ $pallet->container->container_number ?? $pallet->container->container_seal_number }}</p>
                        </div>
                        
                        {{-- Cantidades --}}
                        <div class="pt-0.5">
                            <p class="font-bold text-[12px]">Cajas: <span class="font-normal">{{ $pallet->boxes->count() }}</span></p>
                            <p class="font-bold text-[12px]">Pzas: <span class="font-normal">{{ number_format($pallet->boxes->sum('quantity')) }}</span></p>
                        </div>
                    </div>
                </div>

                {{-- Fila Inferior: Líneas para llenado manual --}}
                <div class="absolute bottom-[2mm] left-[2mm] right-[2mm] text-[11px] font-bold uppercase leading-loose">
                    <div class="flex items-end justify-between mb-0.5">
                        <span class="tracking-wide">Estatus:</span>
                        <div class="border-b-2 border-black flex-1 ml-2 mb-1"></div>
                    </div>
                    <div class="flex items-end justify-between">
                        <span class="tracking-wide">Comentarios:</span>
                        <div class="border-b-2 border-black flex-1 ml-2 mb-1"></div>
                    </div>
                </div>

            </div>
            
            <button onclick="window.print()" class="mt-6 md:hidden items-center px-8 py-3 bg-slate-800 text-white rounded-lg text-lg font-bold shadow-md w-full">
                <i class="fas fa-print mr-2"></i> Imprimir Etiqueta
            </button>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const barcodes = document.querySelectorAll('.barcode-svg');
            barcodes.forEach(function(svg) {
                const code = svg.getAttribute('data-barcode');
                JsBarcode(svg, code, {
                    format: "CODE128",
                    width: 1.5,       
                    height: 25,       
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
                margin: 0mm !important; 
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
                width: 100vw !important; /* Usamos el 100% del lienzo de la impresora */
                height: 100vh !important; 
                margin: 0 !important;
                padding: 3mm !important; /* Un margen interno seguro en milímetros */
                border: none !important; 
                box-shadow: none !important;
                box-sizing: border-box !important;
                overflow: hidden !important; /* Corta cualquier excedente para evitar scroll invisible */
            }
        }
    </style>
    @endpush
</x-app-layout>