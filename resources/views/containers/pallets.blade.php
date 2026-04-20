<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('containers.packing', $container) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div class="bg-gradient-to-br from-purple-600 to-purple-800 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-pallet text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">Armado de Tarimas</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Contenedor {{ $container->container_number }}</p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-3">
                <form method="POST" action="{{ route('containers.create-pallet', $container) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-500 transition text-sm font-medium">
                        <i class="fas fa-plus mr-1"></i> Nueva Tarima
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            {{-- KPIs --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['total_pallets'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total tarimas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pallets_open'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Abiertas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $stats['pallets_closed'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Cerradas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-indigo-600">{{ $stats['available_boxes'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Cajas disponibles</p>
                </div>
            </div>

            {{-- Botón móvil --}}
            <div class="md:hidden">
                <form method="POST" action="{{ route('containers.create-pallet', $container) }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i> Nueva Tarima
                    </button>
                </form>
            </div>

            {{-- Lista de tarimas --}}
            @forelse($container->pallets->sortByDesc('created_at') as $pallet)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden" x-data="{ expanded: {{ $pallet->status === 'abierta' ? 'true' : 'false' }}, showAssign: false }">
                    {{-- Header de la tarima --}}
                    <div class="px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 cursor-pointer border-b border-gray-200 dark:border-gray-700" @click="expanded = !expanded">
                        <div class="flex items-center space-x-3">
                            <div class="bg-purple-100 dark:bg-purple-900/30 p-2.5 rounded-lg">
                                <i class="fas fa-pallet text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white">{{ $pallet->pallet_code }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $pallet->boxes->count() }} cajas · {{ $pallet->boxes->sum('quantity') }} piezas
                                    · Creada {{ $pallet->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @php
                                $palletBadge = $pallet->status === 'abierta'
                                    ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
                                    : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
                            @endphp
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $palletBadge }}">
                                {{ ucfirst($pallet->status) }}
                            </span>
                            <a href="{{ route('pallets.show', $pallet) }}" class="text-purple-600 hover:text-purple-800 dark:text-purple-400 text-sm" title="Ver etiqueta maestra" @click.stop>
                                <i class="fas fa-qrcode"></i>
                            </a>
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Contenido expandible --}}
                    <div x-show="expanded" x-collapse x-cloak>
                        {{-- Cajas en esta tarima --}}
                        <div class="px-6 py-3">
                            @if($pallet->boxes->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($pallet->boxes->sortBy('box_code') as $box)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                            <div>
                                                <p class="font-mono font-bold text-sm text-gray-900 dark:text-white">{{ $box->box_code }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[200px]">{{ $box->containerItem?->product_description ?? '' }}</p>
                                                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">{{ $box->quantity }} pzas</p>
                                            </div>
                                            @if($pallet->status === 'abierta')
                                                <form method="POST" action="{{ route('boxes.remove', $box) }}" class="ml-2">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-red-400 hover:text-red-600 text-xs" title="Retirar de tarima">
                                                        <i class="fas fa-times-circle text-lg"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">
                                    <i class="fas fa-inbox mr-1"></i> Sin cajas asignadas. Haga clic en "Agregar cajas" para comenzar.
                                </p>
                            @endif
                        </div>

                        {{-- Acciones de la tarima --}}
                        @if($pallet->status === 'abierta')
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 space-y-4">
                                {{-- Botón para mostrar el selector de cajas --}}
                                <button @click="showAssign = !showAssign" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                    <i class="fas fa-plus-circle mr-1"></i> Agregar cajas a esta tarima
                                </button>

                                {{-- Selector de cajas disponibles --}}
                                <div x-show="showAssign" x-collapse x-cloak>
                                    @if($availableBoxes->where('container_id', $container->id)->count() > 0)
                                        <form method="POST" action="{{ route('pallets.assign-boxes', $pallet) }}">
                                            @csrf
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 mb-3 max-h-48 overflow-y-auto">
                                                @foreach($availableBoxes as $ab)
                                                    <label class="flex items-center p-2 bg-white dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600 cursor-pointer hover:border-indigo-400 transition">
                                                        <input type="checkbox" name="box_ids[]" value="{{ $ab->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                                                        <div class="text-xs">
                                                            <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $ab->box_code }}</span>
                                                            <span class="text-gray-500 ml-1">{{ $ab->quantity }} pzas</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500 text-sm transition">
                                                <i class="fas fa-check mr-1"></i> Asignar seleccionadas
                                            </button>
                                        </form>
                                    @else
                                        <p class="text-sm text-gray-400">No hay cajas disponibles. Cree más cajas desde la vista de Empaque.</p>
                                    @endif
                                </div>

                                {{-- Cerrar tarima --}}
                                @if($pallet->boxes->count() > 0)
                                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                        <form method="POST" action="{{ route('pallets.close', $pallet) }}" onsubmit="return confirm('¿Cerrar tarima {{ $pallet->pallet_code }}? Ya no se podrán agregar o quitar cajas.')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-500 text-sm transition">
                                                <i class="fas fa-lock mr-1"></i> Cerrar Tarima
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
                    <i class="fas fa-pallet text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">No hay tarimas creadas</p>
                    <p class="text-sm text-gray-400 mt-1">Cree una tarima y asígnele las cajas empacadas</p>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
