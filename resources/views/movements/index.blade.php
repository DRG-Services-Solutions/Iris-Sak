<x-app-layout>
    {{-- Slot para el encabezado de la página --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-blue-700 to-slate-900 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-exchange-alt text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        Historial de Movimientos
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Log de Entradas, Salidas y Ajustes</p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <div class="text-center px-4 py-2 bg-slate-700 rounded-lg">
                    <p class="text-2xl font-bold text-white">{{ $movements->total() }}</p>
                    <p class="text-xs text-gray-300">Registros Totales</p>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Contenido principal --}}
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Barra de búsqueda y filtros --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                    <div class="flex-1 max-w-lg">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   placeholder="Buscar por producto, usuario o ID...">
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('movements.create') }}" 
                           class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-700 to-slate-900 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-blue-600 hover:to-slate-800 shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Registrar Movimiento
                        </a>
                    </div>
                </div>
            </div>

            {{-- Vista DESKTOP --}}
            <div class="hidden md:block bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-slate-700 to-slate-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-100 uppercase tracking-wider">
                                <i class="far fa-calendar-alt mr-2"></i>Fecha / ID
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-100 uppercase tracking-wider">
                                <i class="fas fa-box mr-2"></i>Producto
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-100 uppercase tracking-wider">
                                <i class="fas fa-tag mr-2"></i>Tipo
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-100 uppercase tracking-wider">
                                <i class="fas fa-hashtag mr-2"></i>Cantidad
                            </th>
                            {{-- 
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-100 uppercase tracking-wider">
                                <i class="fas fa-user mr-2"></i>Usuario
                            </th>
                            --}}
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-100 uppercase tracking-wider">
                                <i class="fas fa-cog mr-2"></i>Acciones
                            </th>
                            
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($movements as $movement)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="far fa-clock text-gray-400 mr-2"></i>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $movement->created_at->format('d/m/Y H:i') }}</div>
                                            <div class="text-xs text-gray-500">MOV-{{ str_pad($movement->id, 6, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-start">
                                        <i class="fas fa-cube text-gray-400 mr-2 mt-1"></i>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ str($movement->product->name)->title() }}</div>
                                            <div class="text-xs text-gray-500 font-mono">
                                                <i class="fas fa-barcode mr-1"></i>{{ $movement->product->barcode }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $colors = [
                                            'in' => 'bg-green-100 text-green-800 border-green-200',
                                            'out' => 'bg-red-100 text-red-800 border-red-200',
                                            'adjustment' => 'bg-amber-100 text-amber-800 border-amber-200'
                                        ];
                                        $icons = [
                                            'in' => 'fa-arrow-circle-down',
                                            'out' => 'fa-arrow-circle-up',
                                            'adjustment' => 'fa-sliders-h'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $colors[$movement->type] }}">
                                        <i class="fas {{ $icons[$movement->type] }} mr-1"></i>
                                        {{ str($movement->type)->upper() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center font-bold text-lg {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                        <i class="fas {{ $movement->type === 'in' ? 'fa-plus-circle' : 'fa-minus-circle' }} mr-1"></i>
                                        {{ $movement->quantity }}
                                    </span>
                                </td>
                                {{-- 
                                <td class="px-6 py-4">
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-user-circle text-gray-400 mr-2"></i>
                                        {{ $movement->user->name }}
                                    </div>
                                </td>
                                --}}
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('movements.show', $movement) }}" 
                                    class="inline-flex flex-col items-center justify-center gap-1 px-3 py-2 text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all group">
                                        <i class="fas fa-eye text-xl group-hover:scale-110 transition-transform"></i>
                                        <span class="text-xs font-semibold uppercase tracking-wider">Detalles</span>
                                    </a>
                                </td>
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <i class="fas fa-inbox text-gray-300 dark:text-gray-600 text-6xl"></i>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm font-semibold">No hay movimientos registrados</p>
                                        <p class="text-gray-400 dark:text-gray-500 text-xs">Los movimientos aparecerán aquí una vez que los registres</p>
                                        <a href="{{ route('movements.create') }}" 
                                           class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                            <i class="fas fa-plus mr-2"></i>
                                            Crear Primer Movimiento
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Vista MÓVIL (Cards) --}}
            <div class="md:hidden space-y-4">
                @forelse ($movements as $movement)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border-l-4 {{ $movement->type === 'in' ? 'border-green-500' : ($movement->type === 'out' ? 'border-red-500' : 'border-amber-500') }} overflow-hidden">
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-start space-x-2">
                                    <i class="fas fa-cube text-gray-400 mt-1"></i>
                                    <div>
                                        <h3 class="font-bold text-gray-900 dark:text-gray-100">{{ str($movement->product->name)->title() }}</h3>
                                        <p class="text-xs text-gray-500 font-mono mt-1">
                                            <i class="fas fa-barcode mr-1"></i>{{ $movement->product->barcode }}
                                        </p>
                                    </div>
                                </div>
                                <span class="text-xs font-mono text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                    #{{ str_pad($movement->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-tag text-gray-400"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Tipo:</p>
                                        @if ( $movement->type === 'in' )
                                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold border border-green-200">
                                                <i class="fas fa-arrow-circle-down mr-1"></i>ENTRADA
                                            </span>
                                        @elseif ( $movement->type === 'out' )
                                            <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold border border-red-200">
                                                <i class="fas fa-arrow-circle-up mr-1"></i>SALIDA
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold border border-amber-200">
                                                <i class="fas fa-sliders-h mr-1"></i>AJUSTE
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-end space-x-2">
                                    <i class="fas fa-hashtag text-gray-400"></i>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Cantidad:</p>
                                        <span class="font-bold text-lg {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                            <i class="fas {{ $movement->type === 'in' ? 'fa-plus' : 'fa-minus' }}"></i>
                                            {{ $movement->quantity }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $movement->created_at->format('d/m/Y H:i') }}
                                </div>
                                {{-- 
                                <div class="flex items-center text-xs text-gray-500">
                                    <i class="fas fa-user mr-1"></i>
                                    {{ $movement->user->name }}
                                </div>
                                --}}
                                <a href="{{ route('movements.show', $movement) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>
                                    Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                        <div class="p-8 text-center">
                            <i class="fas fa-inbox text-gray-300 dark:text-gray-600 text-6xl mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-semibold mb-2">No hay movimientos registrados</p>
                            <p class="text-gray-400 dark:text-gray-500 text-xs mb-4">Los movimientos aparecerán aquí una vez que los registres</p>
                            <a href="{{ route('movements.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Crear Primer Movimiento
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>