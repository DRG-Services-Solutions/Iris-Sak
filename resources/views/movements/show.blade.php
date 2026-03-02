<x-app-layout>
    {{-- Encabezado --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-blue-700 to-slate-900 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-file-invoice text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        Detalle de Movimiento
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        MOV-{{ str_pad($movement->id, 6, '0', STR_PAD_LEFT) }}
                    </p>
                </div>
            </div>
            <a href="{{ route('movements.index') }}" 
               class="hidden md:inline-flex items-center px-4 py-2 bg-slate-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-200 hover:bg-slate-200 dark:hover:bg-gray-600 transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al Historial
            </a>
        </div>
    </x-slot>

    {{-- Contenido Principal --}}
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Alerta de Tipo de Movimiento --}}
            <div class="mb-6">
                @php
                    $alertStyles = [
                        'in' => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800',
                        'out' => 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800',
                        'adjustment' => 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800'
                    ];
                    $textStyles = [
                        'in' => 'text-green-800 dark:text-green-300',
                        'out' => 'text-red-800 dark:text-red-300',
                        'adjustment' => 'text-amber-800 dark:text-amber-300'
                    ];
                    $icons = [
                        'in' => 'fa-arrow-circle-down',
                        'out' => 'fa-arrow-circle-up',
                        'adjustment' => 'fa-sliders-h'
                    ];
                    $labels = [
                        'in' => 'ENTRADA AL INVENTARIO',
                        'out' => 'SALIDA DEL INVENTARIO',
                        'adjustment' => 'AJUSTE DE INVENTARIO'
                    ];
                @endphp
                
                <div class="rounded-lg border-2 {{ $alertStyles[$movement->type] }} p-4">
                    <div class="flex items-center">
                        <i class="fas {{ $icons[$movement->type] }} {{ $textStyles[$movement->type] }} text-2xl mr-3"></i>
                        <div class="flex-1">
                            <h3 class="font-bold {{ $textStyles[$movement->type] }} text-lg">
                                {{ $labels[$movement->type] }}
                            </h3>
                            <p class="text-sm {{ $textStyles[$movement->type] }} opacity-80 mt-1">
                                Operación registrada el {{ $movement->created_at->format('d/m/Y') }} a las {{ $movement->created_at->format('H:i:s') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs {{ $textStyles[$movement->type] }} opacity-70 font-semibold uppercase">Cantidad</p>
                            <p class="text-3xl font-black {{ $textStyles[$movement->type] }}">
                                {{ $movement->type === 'in' ? '+' : ($movement->type === 'out' ? '-' : '') }}{{ $movement->quantity }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Columna Principal (Información del Producto y Movimiento) --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Información del Producto --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4 border-b border-slate-600">
                            <div class="flex items-center">
                                <i class="fas fa-cube text-white mr-3"></i>
                                <h3 class="text-lg font-semibold text-white">Información del Producto</h3>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                {{-- Ícono del producto --}}
                                <div class="flex-shrink-0">
                                    <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box-open text-blue-600 dark:text-blue-300 text-3xl"></i>
                                    </div>
                                </div>
                                
                                {{-- Detalles del producto --}}
                                <div class="flex-1">
                                    <h4 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                        {{ str($movement->product->name)->title() }}
                                    </h4>
                                    
                                    <div class="space-y-3 mt-4">
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-barcode text-gray-400 w-5 mr-3"></i>
                                            <span class="text-gray-600 dark:text-gray-400">Código de Barras:</span>
                                            <span class="ml-2 font-mono font-bold text-gray-900 dark:text-gray-100">{{ $movement->product->barcode }}</span>
                                        </div>
                                        
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-layer-group text-gray-400 w-5 mr-3"></i>
                                            <span class="text-gray-600 dark:text-gray-400">Stock Actual:</span>
                                            <span class="ml-2 font-bold text-gray-900 dark:text-gray-100">{{ $movement->product->stock }} unidades</span>
                                        </div>
                                        
                                        @if($movement->product->description)
                                        <div class="flex items-start text-sm">
                                            <i class="fas fa-align-left text-gray-400 w-5 mr-3 mt-1"></i>
                                            <div>
                                                <span class="text-gray-600 dark:text-gray-400">Descripción:</span>
                                                <p class="ml-0 mt-1 text-gray-900 dark:text-gray-100">{{ $movement->product->description }}</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Detalles del Movimiento --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4 border-b border-slate-600">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-white mr-3"></i>
                                <h3 class="text-lg font-semibold text-white">Detalles del Movimiento</h3>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <dl class="space-y-4">
                                <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                                    <dt class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-hashtag text-gray-400 w-5 mr-3"></i>
                                        ID del Movimiento
                                    </dt>
                                    <dd class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        MOV-{{ str_pad($movement->id, 6, '0', STR_PAD_LEFT) }}
                                    </dd>
                                </div>
                                
                                <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                                    <dt class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-exchange-alt text-gray-400 w-5 mr-3"></i>
                                        Tipo de Operación
                                    </dt>
                                    <dd>
                                        @if($movement->type === 'in')
                                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-full text-xs font-bold border border-green-200 dark:border-green-800">
                                                <i class="fas fa-arrow-circle-down mr-1"></i>ENTRADA
                                            </span>
                                        @elseif($movement->type === 'out')
                                            <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full text-xs font-bold border border-red-200 dark:border-red-800">
                                                <i class="fas fa-arrow-circle-up mr-1"></i>SALIDA
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 rounded-full text-xs font-bold border border-amber-200 dark:border-amber-800">
                                                <i class="fas fa-sliders-h mr-1"></i>AJUSTE
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                
                                <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                                    <dt class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-sort-numeric-up text-gray-400 w-5 mr-3"></i>
                                        Cantidad
                                    </dt>
                                    <dd class="text-2xl font-black {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $movement->type === 'in' ? '+' : ($movement->type === 'out' ? '-' : '') }}{{ $movement->quantity }}
                                    </dd>
                                </div>
                                
                                <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                                    <dt class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400">
                                        <i class="far fa-calendar-alt text-gray-400 w-5 mr-3"></i>
                                        Fecha de Registro
                                    </dt>
                                    <dd class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        {{ $movement->created_at->format('d/m/Y H:i:s') }}
                                    </dd>
                                </div>
                                
                                @if($movement->user)
                                <div class="flex items-center justify-between py-3">
                                    <dt class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-user-circle text-gray-400 w-5 mr-3"></i>
                                        Registrado Por
                                    </dt>
                                    <dd class="flex items-center text-sm font-bold text-gray-900 dark:text-gray-100">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-white text-xs font-bold">{{ substr($movement->user->name, 0, 1) }}</span>
                                        </div>
                                        {{ $movement->user->name }}
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    {{-- Notas/Justificación (si existen) --}}
                    @if($movement->notes)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4 border-b border-slate-600">
                            <div class="flex items-center">
                                <i class="fas fa-sticky-note text-white mr-3"></i>
                                <h3 class="text-lg font-semibold text-white">Justificación de Auditoría</h3>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border-l-4 border-blue-500">
                                <i class="fas fa-quote-left text-gray-300 dark:text-gray-600 mb-2"></i>
                                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                                    {{ $movement->notes }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Sidebar (Impacto y Timeline) --}}
                <div class="space-y-6">
                    
                    {{-- Widget de Impacto en Stock --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4 border-b border-slate-600">
                            <div class="flex items-center">
                                <i class="fas fa-chart-line text-white mr-3"></i>
                                <h3 class="text-sm font-semibold text-white">Impacto en Stock</h3>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            @php
                                $currentStock = $movement->product->stock;
                                $qty = $movement->quantity;

                                $stockBefore = match ($movement->type) {
                                    'in'         => max(0, $currentStock - $qty),
                                    'out'        => $currentStock + $qty,
                                    'adjustment' => $currentStock,
                                    default      => $currentStock,
                                };
                            @endphp
                            
                            <div class="space-y-4">
                                {{-- Stock Anterior --}}
                                <div class="text-center p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold mb-1">Stock Anterior</p>
                                    <p class="text-3xl font-black text-gray-700 dark:text-gray-300">{{ $stockBefore }}</p>
                                </div>
                                
                                {{-- Flecha e Indicador --}}
                                <div class="flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-arrow-down text-3xl {{ $movement->type === 'in' ? 'text-green-500' : ($movement->type === 'out' ? 'text-red-500' : 'text-amber-500') }}"></i>
                                        <p class="mt-2 text-xl font-black {{ $movement->type === 'in' ? 'text-green-600' : ($movement->type === 'out' ? 'text-red-600' : 'text-amber-600') }}">
                                            {{ $movement->type === 'in' ? '+' : ($movement->type === 'out' ? '-' : '~') }}{{ $movement->quantity }}
                                        </p>
                                    </div>
                                </div>
                                
                                {{-- Stock Actual --}}
                                <div class="text-center p-4 bg-gradient-to-br {{ $movement->type === 'in' ? 'from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20' : ($movement->type === 'out' ? 'from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20' : 'from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20') }} rounded-lg border-2 {{ $movement->type === 'in' ? 'border-green-200 dark:border-green-800' : ($movement->type === 'out' ? 'border-red-200 dark:border-red-800' : 'border-amber-200 dark:border-amber-800') }}">
                                    <p class="text-xs {{ $movement->type === 'in' ? 'text-green-700 dark:text-green-400' : ($movement->type === 'out' ? 'text-red-700 dark:text-red-400' : 'text-amber-700 dark:text-amber-400') }} uppercase font-bold mb-1">Stock Resultante</p>
                                    <p class="text-4xl font-black {{ $movement->type === 'in' ? 'text-green-700 dark:text-green-400' : ($movement->type === 'out' ? 'text-red-700 dark:text-red-400' : 'text-amber-700 dark:text-amber-400') }}">
                                        {{ $movement->stock_after }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Información del Sistema --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4 border-b border-slate-600">
                            <div class="flex items-center">
                                <i class="fas fa-server text-white mr-3"></i>
                                <h3 class="text-sm font-semibold text-white">Información del Sistema</h3>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-4 text-xs">
                                <div class="flex items-start">
                                    <i class="fas fa-clock text-gray-400 mr-2 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-gray-500 dark:text-gray-400">Creado</p>
                                        <p class="font-mono text-gray-700 dark:text-gray-300">{{ $movement->created_at->format('Y-m-d H:i:s') }}</p>
                                        <p class="text-gray-400 text-xs mt-1">{{ $movement->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <i class="fas fa-edit text-gray-400 mr-2 mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-gray-500 dark:text-gray-400">Última Actualización</p>
                                        <p class="font-mono text-gray-700 dark:text-gray-300">{{ $movement->updated_at->format('Y-m-d H:i:s') }}</p>
                                        <p class="text-gray-400 text-xs mt-1">{{ $movement->updated_at->diffForHumans() }}</p>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
                                        <span class="font-semibold uppercase tracking-wider">Registro Inmutable</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones Rápidas --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="p-6 space-y-3">
                            <a href="{{ route('products.show', $movement->product) }}" 
                               class="flex items-center justify-center w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition-colors">
                                <i class="fas fa-box mr-2"></i>
                                Ver Producto Completo
                            </a>
                            
                            <a href="{{ route('movements.index') }}" 
                               class="flex items-center justify-center w-full px-4 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold text-sm rounded-lg transition-colors">
                                <i class="fas fa-list mr-2"></i>
                                Ver Todos los Movimientos
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <x-industrial-footer />
        </div>
    </div>
</x-app-layout>