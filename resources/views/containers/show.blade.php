<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('containers.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div class="bg-gradient-to-br from-teal-600 to-teal-800 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-shipping-fast text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">ID:{{ $container->container_seal_number }} </h2>
                    <h3 class="text-sm text-gray-500 dark:text-gray-400">{{ $container->container_number ? 'CONTENEDOR: ' . $container->container_number : 'Sin número de contenedor' }}</h3>
                    @if($container->status === 'abierto')
                        <span class="px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded-full border border-blue-200 dark:border-blue-800">En Proceso</span>
                    @else
                        <span class="px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded-full border border-gray-200 dark:border-gray-600">Cerrado</span>
                    @endif
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-2">
                <a href="{{ route('containers.inspection', $container) }}" class="px-3 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-500 transition text-sm font-medium"><i class="fas fa-tags mr-1"></i> Inspección</a>
                <a href="{{ route('containers.packing', $container) }}" class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500 transition text-sm font-medium"><i class="fas fa-box mr-1"></i> Empaque</a>
                <a href="{{ route('containers.pallets', $container) }}" class="px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-500 transition text-sm font-medium"><i class="fas fa-pallet mr-1"></i> Tarimas</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>
            @endif

            {{-- ================================================================ --}}
            {{-- MÓDULO DE ESCANEO DE CAJAS                                       --}}
            {{-- ================================================================ --}}
            @if($container->status !== 'cerrado')
            <div x-data="scannerModule()" x-init="init()" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border-2 border-teal-500/50">
                <div class="px-4 md:px-6 py-3 bg-gradient-to-r from-teal-600 to-teal-700 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-barcode text-white text-xl"></i>
                        <h3 class="text-lg font-bold text-white">Escaneo de Cajas</h3>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span x-show="isReady" class="flex items-center space-x-1.5 text-teal-100 text-sm">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-300 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-400"></span>
                            </span>
                            <span class="hidden sm:inline">Listo para escanear</span>
                        </span>
                        <span x-show="isProcessing" class="flex items-center space-x-1.5 text-yellow-200 text-sm">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Procesando...</span>
                        </span>
                    </div>
                </div>

                <div class="p-4 md:p-6">
                    {{-- Input de escaneo --}}
                    <div class="relative">
                        <input
                            x-ref="scanInput"
                            x-model="barcode"
                            @keydown.enter.prevent="scan()"
                            type="text"
                            inputmode="none"
                            autocomplete="off"
                            class="w-full border-2 border-teal-400 dark:border-teal-600 rounded-lg bg-white dark:bg-gray-700 text-lg py-3 px-4 pr-24 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono tracking-wider placeholder-gray-400"
                            placeholder="Esperando escaneo..."
                            :disabled="isProcessing"
                        >
                        <button
                            @click="scan()"
                            :disabled="!barcode || isProcessing"
                            class="absolute right-2 top-1/2 -translate-y-1/2 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-500 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm font-medium"
                        >
                            <i class="fas fa-plus mr-1"></i> +1
                        </button>
                    </div>

                    {{-- Feedback del último escaneo --}}
                    <div x-show="lastScan" x-transition.duration.300ms class="mt-3">
                        <div
                            :class="{
                                'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700 text-green-800 dark:text-green-200': lastScan?.success,
                                'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700 text-red-800 dark:text-red-200': lastScan && !lastScan.success,
                            }"
                            class="p-3 rounded-lg border flex items-center justify-between"
                        >
                            <div class="flex items-center space-x-2 min-w-0">
                                <i :class="lastScan?.success ? 'fas fa-check-circle text-green-500' : 'fas fa-exclamation-circle text-red-500'" class="text-lg flex-shrink-0"></i>
                                <div class="min-w-0">
                                    <span class="font-medium text-sm block truncate" x-text="lastScan?.message"></span>
                                    <span x-show="lastScan?.success" class="block text-xs opacity-75 truncate" x-text="lastScan?.detail"></span>
                                </div>
                            </div>
                            <button @click="lastScan = null" class="text-gray-400 hover:text-gray-600 flex-shrink-0 ml-2"><i class="fas fa-times"></i></button>
                        </div>
                    </div>

                    {{-- Log compacto: colapsable, máx 10 --}}
                    <div x-show="scanLog.length > 0" class="mt-3" x-data="{ showLog: false }">
                        <button @click="showLog = !showLog" class="flex items-center justify-between w-full text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition py-1">
                            <span class="font-semibold uppercase tracking-wider">
                                <i class="fas fa-history mr-1"></i>
                                Escaneos (<span x-text="scanLog.length"></span>)
                            </span>
                            <span class="flex items-center space-x-3">
                                <span @click.stop="scanLog = []; showLog = false" class="text-gray-400 hover:text-red-500 cursor-pointer" title="Limpiar log">
                                    <i class="fas fa-eraser"></i>
                                </span>
                                <i :class="showLog ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-gray-400"></i>
                            </span>
                        </button>
                        <div x-show="showLog" x-collapse x-cloak class="mt-2 max-h-36 overflow-y-auto space-y-1">
                            <template x-for="(entry, idx) in scanLog" :key="idx">
                                <div
                                    :class="entry.success ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'"
                                    class="flex items-center justify-between text-xs py-1 px-2 rounded bg-gray-50 dark:bg-gray-700/50"
                                >
                                    <span class="flex items-center space-x-2 min-w-0">
                                        <i :class="entry.success ? 'fas fa-check text-green-500' : 'fas fa-times text-red-500'" class="flex-shrink-0"></i>
                                        <span class="font-mono truncate" x-text="entry.barcode"></span>
                                    </span>
                                    <span class="text-gray-400 flex-shrink-0 ml-2 tabular-nums" x-text="entry.time"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- KPIs --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($container->declared_qty) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Piezas declaradas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold {{ $container->received_qty >= $container->declared_qty ? 'text-green-600' : 'text-amber-600' }}" id="kpi-received">{{ number_format($container->received_qty) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Piezas recibidas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-slate-700 dark:text-slate-300">{{ $container->total_cartons }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Cajas declaradas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    @php $totalReceivedCartons = $container->items->sum('received_cartons'); @endphp
                    <p class="text-2xl font-bold {{ $totalReceivedCartons >= $container->total_cartons ? 'text-green-600' : 'text-amber-600' }}" id="kpi-received-cartons">{{ $totalReceivedCartons }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Cajas recibidas</p>
                </div>
            </div>

            {{-- Info del contenedor --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-3 text-sm">
                    <div class="flex flex-col border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Recibido por</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $container->receivedByUser?->name ?? '—' }}</span>
                    </div>
                    <div class="flex flex-col border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Fecha de registro</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $container->received_at?->format('d/M/Y H:i') ?? $container->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex flex-col border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Estatus aduanal</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $container->customs_status ?? 'pendiente')) }}</span>
                    </div>
                </div>

                @if($container->notes)
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 italic">{{ $container->notes }}</p>
                @endif

                {{-- Acciones --}}
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-wrap items-center gap-3">
                    <form method="POST" action="{{ route('containers.update-customs', $container) }}" class="flex items-center space-x-2">
                        @csrf @method('PATCH')
                        <select name="customs_status" class="border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 focus:ring-2 focus:ring-teal-500 text-gray-900 dark:text-gray-100">
                            @foreach(['pendiente', 'en_revision', 'liberado', 'retenido'] as $cs)
                                <option value="{{ $cs }}" {{ $container->customs_status === $cs ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cs)) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 text-sm transition"><i class="fas fa-sync-alt mr-1"></i> Aduana</button>
                    </form>
                    @if($container->status !== 'cerrado')
                        <form method="POST" action="{{ route('containers.close', $container) }}" onsubmit="return confirm('¿Cerrar este contenedor?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-500 text-sm transition"><i class="fas fa-lock mr-1"></i> Cerrar contenedor</button>
                        </form>
                    @endif
                    <div class="md:hidden flex flex-wrap gap-2">
                        <a href="{{ route('containers.inspection', $container) }}" class="px-3 py-2 bg-teal-600 text-white rounded-lg text-sm"><i class="fas fa-tags mr-1"></i> Inspección</a>
                        <a href="{{ route('containers.packing', $container) }}" class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm"><i class="fas fa-box mr-1"></i> Empaque</a>
                        <a href="{{ route('containers.pallets', $container) }}" class="px-3 py-2 bg-purple-600 text-white rounded-lg text-sm"><i class="fas fa-pallet mr-1"></i> Tarimas</a>
                    </div>
                </div>
            </div>

            {{-- ================================================================ --}}
            {{-- TABLA DE ARTÍCULOS                                               --}}
            {{-- ================================================================ --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white"><i class="fas fa-list-alt text-teal-500 mr-2"></i>Artículos del Packing List</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $container->items->count() }} artículos</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">#</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Barcode</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Descripción</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Pzas/Caja</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Cajas decl.</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Cajas recib.</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Dif.</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Pzas totales</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Estatus</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($container->items as $item)
                                @php
                                    $statusBadge = match($item->status) {
                                        'conforme'     => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                        'faltante'     => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                        'sobrante'     => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                        'no_recibido'  => 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200',
                                        default        => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50" id="item-row-{{ $item->id }}" data-barcode="{{ $item->barcode }}">
                                    <td class="px-3 py-3 text-gray-500">{{ $item->item_number }}</td>
                                    <td class="px-3 py-3">
                                        <span class="font-mono font-bold text-gray-900 dark:text-white text-xs">{{ $item->barcode ?? '—' }}</span>
                                        @if($item->product_code)
                                            <span class="block text-xs text-gray-400 mt-0.5">{{ $item->product_code }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-gray-800 dark:text-gray-200 max-w-xs">
                                        <div class="truncate" title="{{ $item->product_description }}">{{ $item->product_description }}</div>
                                        @if($item->product_description_cn)
                                            <div class="text-xs text-gray-400 truncate">{{ $item->product_description_cn }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $item->pieces_per_carton }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-center font-medium text-gray-800 dark:text-gray-200">{{ $item->carton_count }}</td>

                                    {{-- Cajas recibidas —  +/- --}}
                                    <td class="px-3 py-3 text-center" id="item-received-cell-{{ $item->id }}">
                                        @if($container->status !== 'cerrado')
                                            <div class="inline-flex items-center space-x-1">
                                                <button type="button" onclick="removeCarton({{ $item->id }})"
                                                    class="w-7 h-7 flex items-center justify-center rounded bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 transition text-xs disabled:opacity-30"
                                                    title="Remover 1 caja" id="btn-remove-{{ $item->id }}" {{ $item->received_cartons <= 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                
                                                <input type="number" 
                                                    id="item-received-{{ $item->id }}" 
                                                    value="{{ $item->received_cartons }}"
                                                    min="0"
                                                    onchange="setCartonCount({{ $item->id }}, this)"
                                                    onkeydown="if(event.key === 'Enter') this.blur();"
                                                    onfocus="this.select()"
                                                    class="w-14 text-center font-bold text-lg text-gray-900 dark:text-gray-100 bg-transparent border border-transparent hover:bg-gray-50 dark:hover:bg-gray-700 focus:bg-white dark:focus:bg-gray-800 focus:border-teal-500 focus:ring-2 focus:ring-teal-500 rounded px-1 py-0.5 transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                >

                                                <button type="button" onclick="addCarton({{ $item->id }})"
                                                    class="w-7 h-7 flex items-center justify-center rounded bg-teal-100 text-teal-700 hover:bg-teal-200 dark:bg-teal-900/30 dark:text-teal-400 dark:hover:bg-teal-900/50 transition text-xs"
                                                    title="Agregar 1 caja">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="font-medium">{{ $item->received_cartons }}</span>
                                        @endif
                                    </td>

                                    {{-- Diferencia --}}
                                    <td class="px-3 py-3 text-center" id="item-diff-cell-{{ $item->id }}">
                                        @if($item->carton_difference > 0)
                                            <span class="text-red-600 font-bold">-{{ $item->carton_difference }}</span>
                                        @elseif($item->carton_difference < 0)
                                            <span class="text-blue-600 font-bold">+{{ abs($item->carton_difference) }}</span>
                                        @else
                                            <span class="text-green-600"><i class="fas fa-check"></i></span>
                                        @endif
                                    </td>

                                    {{-- Piezas totales --}}
                                    <td class="px-3 py-3 text-center text-gray-600 dark:text-gray-300" id="item-pcs-cell-{{ $item->id }}">
                                        <span class="font-medium">{{ number_format($item->received_qty) }}</span>
                                        <span class="text-xs text-gray-400 block">de {{ number_format($item->declared_qty) }}</span>
                                    </td>

                                    {{-- Estatus --}}
                                    <td class="px-3 py-3 text-center" id="item-status-cell-{{ $item->id }}">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">
                                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                        </span>
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="px-3 py-3 text-center">
                                        @if($container->status !== 'cerrado')
                                            <div class="flex items-center justify-center space-x-1">
                                                {{-- Nota --}}
                                                <button type="button"
                                                    onclick="openNoteModal({{ $item->id }}, '{{ addslashes($item->barcode ?? $item->product_description) }}', '{{ addslashes($item->notes ?? '') }}', false)"
                                                    class="w-7 h-7 flex items-center justify-center rounded transition text-xs {{ $item->notes ? 'bg-amber-100 text-amber-700 hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400' }}"
                                                    title="{{ $item->notes ? 'Editar nota' : 'Agregar nota' }}" id="btn-note-{{ $item->id }}">
                                                    <i class="fas fa-{{ $item->notes ? 'sticky-note' : 'comment-dots' }}"></i>
                                                </button>
                                                {{-- No recibido --}}
                                                <button type="button"
                                                    onclick="openNoteModal({{ $item->id }}, '{{ addslashes($item->barcode ?? $item->product_description) }}', '', true)"
                                                    class="w-7 h-7 flex items-center justify-center rounded bg-gray-100 text-gray-500 hover:bg-red-100 hover:text-red-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-red-900/30 dark:hover:text-red-400 transition text-xs"
                                                    title="Marcar como no recibido" id="btn-no-recv-{{ $item->id }}">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Fila de nota visible si existe --}}
                                @if($item->notes)
                                    <tr class="bg-amber-50/50 dark:bg-amber-900/10" id="item-note-row-{{ $item->id }}">
                                        <td colspan="10" class="px-4 py-1.5">
                                            <div class="flex items-center gap-2 text-xs">
                                                <i class="fas fa-sticky-note text-amber-400 flex-shrink-0"></i>
                                                <span class="text-gray-600 dark:text-gray-300">{{ $item->notes }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-inbox text-3xl text-gray-300 dark:text-gray-600 mb-2 block"></i>
                                        No hay artículos. Cargue un Packing List o agregue manualmente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Agregar item manual --}}
            @if($container->status !== 'cerrado')
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4"><i class="fas fa-plus-circle text-teal-500 mr-2"></i>Agregar Artículo Manual</h3>
                    <form method="POST" action="{{ route('containers.add-item', $container) }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Barcode</label>
                            <input type="text" name="barcode" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-teal-500" placeholder="EAN/UPC">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Descripción *</label>
                            <input type="text" name="product_description" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-teal-500" placeholder="Descripción del artículo">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Piezas total *</label>
                            <input type="number" name="declared_qty" required min="1" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-teal-500" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Cajas *</label>
                            <input type="number" name="carton_count" required min="1" value="1" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-teal-500" placeholder="1">
                        </div>
                        <div>
                            <button type="submit" class="w-full px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-500 transition text-sm font-medium"><i class="fas fa-plus mr-1"></i> Agregar</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- MODAL DE NOTAS                                                   --}}
    {{-- ================================================================ --}}
    <div
        x-data="noteModal()"
        x-show="open"
        x-cloak
        @open-note-modal.window="openModal($event.detail)"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center"
    >
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" @click="close()"></div>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
            class="relative w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-t-2xl sm:rounded-2xl shadow-xl p-5 sm:p-6 max-h-[85vh] overflow-y-auto"
        >
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-sticky-note text-amber-500 mr-2"></i>
                    <span x-text="isMarkNotReceived ? 'Marcar como no recibido' : 'Nota de discrepancia'"></span>
                </h3>
                <button @click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            {{-- Aviso de no recibido --}}
            <div x-show="isMarkNotReceived" class="mb-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <p class="text-sm text-red-700 dark:text-red-300">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Esto pondrá las cajas recibidas en <strong>0</strong> y marcará el artículo como <strong>no recibido</strong>.
                </p>
            </div>

            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                Artículo: <span class="font-semibold text-gray-700 dark:text-gray-200" x-text="itemLabel"></span>
            </p>

            {{-- Razón predefinida --}}
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Razón</label>
                <select
                    x-model="reason"
                    @change="if (reason && reason !== 'otro') notes = reason"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500"
                >
                    <option value="">— Seleccionar razón —</option>
                    <option value="Faltante en origen">Faltante en origen</option>
                    <option value="Caja dañada / producto dañado">Caja dañada / producto dañado</option>
                    <option value="Sobrante no declarado">Sobrante no declarado</option>
                    <option value="Error en packing list">Error en packing list</option>
                    <option value="Producto no embarcado">Producto no embarcado</option>
                    <option value="Producto equivocado">Producto equivocado</option>
                    <option value="Merma / producto extraviado">Merma / producto extraviado</option>
                    <option value="otro">Otro (especificar)</option>
                </select>
            </div>

            {{-- Nota libre --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Detalle</label>
                <textarea
                    x-model="notes"
                    rows="3"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500"
                    placeholder="Describir el motivo de la discrepancia..."
                ></textarea>
            </div>

            {{-- Feedback --}}
            <div x-show="feedback" x-transition class="mb-3">
                <div
                    :class="feedbackOk ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/20 dark:text-green-300 dark:border-green-800' : 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-800'"
                    class="text-sm p-2 rounded-lg border"
                    x-text="feedback"
                ></div>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <button 
                    x-show="notes.trim().length > 0" 
                    @click="clearNote()" 
                    :disabled="saving"
                    class="px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition mr-auto"
                >
                    <i class="fas fa-trash-alt mr-1"></i> Eliminar nota
                </button>
                <button @click="close()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition">
                    Cancelar
                </button>
                <button
                    @click="save()"
                    :disabled="saving"
                    :class="isMarkNotReceived ? 'bg-red-600 hover:bg-red-500' : 'bg-amber-600 hover:bg-amber-500'"
                    class="px-5 py-2 text-white rounded-lg disabled:opacity-50 transition text-sm font-medium"
                >
                    <i :class="isMarkNotReceived ? 'fas fa-ban' : 'fas fa-save'" class="mr-1"></i>
                    <span x-text="saving ? 'Guardando...' : (isMarkNotReceived ? 'Confirmar no recibido' : 'Guardar nota')"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- JAVASCRIPT                                                       --}}
    {{-- ================================================================ --}}
    @push('scripts')
    <script>
        const CONTAINER_ID = {{ $container->id }};
        const CSRF_TOKEN   = '{{ csrf_token() }}';
        const SCAN_URL     = '{{ route("containers.scan", $container) }}';
        const ITEMS_BASE   = '{{ url("containers/{$container->id}/items") }}';

        /* ── Audio ──────────────────────────────────────────────────── */
        function playBeep(success = true) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain); gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.value = success ? 880 : 300;
                gain.gain.value = 0.3;
                osc.start();
                osc.stop(ctx.currentTime + (success ? 0.12 : 0.3));
            } catch (e) {}
        }

        /* ── Flash ──────────────────────────────────────────────────── */
        function flashRow(itemId, success = true) {
            const row = document.getElementById(`item-row-${itemId}`);
            if (!row) return;
            const cls = success ? ['bg-green-100', 'dark:bg-green-900/30'] : ['bg-red-100', 'dark:bg-red-900/30'];
            row.classList.add(...cls);
            setTimeout(() => row.classList.remove(...cls), 1200);
        }

        /* ── Update UI celdas ───────────────────────────────────────── */
        function updateItemUI(data) {
            const el = (id) => document.getElementById(id);

            // Cajas recibidas
            const recv = el(`item-received-${data.item_id}`);
            if (recv) {
                if (recv.tagName === 'INPUT') {
                    recv.value = data.received_cartons;
                } else {
                    recv.textContent = data.received_cartons;
                }
            }

            // Botón remover
            const btnRm = el(`btn-remove-${data.item_id}`);
            if (btnRm) btnRm.disabled = data.received_cartons <= 0;

            // Diferencia
            const diff = el(`item-diff-cell-${data.item_id}`);
            if (diff) {
                const d = data.carton_difference;
                diff.innerHTML = d > 0
                    ? `<span class="text-red-600 font-bold">-${d}</span>`
                    : d < 0
                        ? `<span class="text-blue-600 font-bold">+${Math.abs(d)}</span>`
                        : `<span class="text-green-600"><i class="fas fa-check"></i></span>`;
            }

            // Piezas
            const pcs = el(`item-pcs-cell-${data.item_id}`);
            if (pcs) {
                pcs.innerHTML = `<span class="font-medium">${Number(data.received_qty).toLocaleString()}</span>
                    <span class="text-xs text-gray-400 block">de ${Number(data.declared_qty).toLocaleString()}</span>`;
            }

            // Status
            const st = el(`item-status-cell-${data.item_id}`);
            if (st) {
                const badges = {
                    'conforme':    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                    'faltante':    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                    'sobrante':    'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                    'no_recibido': 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200',
                    'pendiente':   'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                };
                const cls = badges[data.status] || badges['pendiente'];
                const lbl = (data.status || 'pendiente').replace('_', ' ');
                st.innerHTML = `<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${cls}">${lbl.charAt(0).toUpperCase() + lbl.slice(1)}</span>`;
            }

            // KPIs
            const kc = el('kpi-received-cartons');
            if (kc && data.total_received_cartons !== undefined) kc.textContent = data.total_received_cartons;
            const kr = el('kpi-received');
            if (kr && data.total_received_qty !== undefined) kr.textContent = Number(data.total_received_qty).toLocaleString();
        }

        /* ── Nota row update ────────────────────────────────────────── */
        function updateNoteRow(itemId, noteText) {
            let noteRow = document.getElementById(`item-note-row-${itemId}`);
            const itemRow = document.getElementById(`item-row-${itemId}`);
            if (!itemRow) return;

            if (noteText) {
                const noteHtml = `<td colspan="10" class="px-4 py-1.5"><div class="flex items-center gap-2 text-xs"><i class="fas fa-sticky-note text-amber-400 flex-shrink-0"></i><span class="text-gray-600 dark:text-gray-300">${noteText}</span></div></td>`;
                if (noteRow) {
                    noteRow.innerHTML = noteHtml;
                } else {
                    noteRow = document.createElement('tr');
                    noteRow.id = `item-note-row-${itemId}`;
                    noteRow.className = 'bg-amber-50/50 dark:bg-amber-900/10';
                    noteRow.innerHTML = noteHtml;
                    itemRow.after(noteRow);
                }

                // Poner el botón del ícono en amarillo
                const btnNote = document.getElementById(`btn-note-${itemId}`);
                if (btnNote) {
                    btnNote.classList.remove('bg-gray-100', 'text-gray-500', 'dark:bg-gray-700', 'dark:text-gray-400');
                    btnNote.classList.add('bg-amber-100', 'text-amber-700', 'dark:bg-amber-900/30', 'dark:text-amber-400');
                    btnNote.querySelector('i').className = 'fas fa-sticky-note';
                }
            } else {
                // SI SE BORRÓ LA NOTA: Remover la fila amarilla y regresar el botón a gris
                if (noteRow) noteRow.remove();
                
                const btnNote = document.getElementById(`btn-note-${itemId}`);
                if (btnNote) {
                    btnNote.classList.remove('bg-amber-100', 'text-amber-700', 'dark:bg-amber-900/30', 'dark:text-amber-400');
                    btnNote.classList.add('bg-gray-100', 'text-gray-500', 'dark:bg-gray-700', 'dark:text-gray-400');
                    btnNote.querySelector('i').className = 'fas fa-comment-dots';
                }
            }
        }

        /* ── Ajustar cajas ──────────────────────────────────────────── */
        async function adjustCarton(itemId, action) {
            try {
                const res = await fetch(`${ITEMS_BASE}/${itemId}/${action}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                });
                const data = await res.json();
                if (data.success) {
                    updateItemUI(data);
                    flashRow(itemId, true);
                    playBeep(true);
                } else {
                    flashRow(itemId, false);
                    playBeep(false);
                }
                return data;
            } catch (err) {
                playBeep(false);
                return { success: false, message: 'Error de conexión' };
            }
        }

        function addCarton(itemId)    { adjustCarton(itemId, 'add-carton'); }
        function removeCarton(itemId) { adjustCarton(itemId, 'remove-carton'); }
        async function setCartonCount(itemId, inputElement) {
            const newValue = parseInt(inputElement.value);
            
            // Validación básica para evitar letras o números negativos
            if (isNaN(newValue) || newValue < 0) {
                inputElement.value = inputElement.defaultValue; // Regresa al valor anterior
                return;
            }

            try {
                // Bloquear el input mientras procesa
                inputElement.disabled = true;

                const res = await fetch(`${ITEMS_BASE}/${itemId}/set-cartons`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': CSRF_TOKEN, 
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify({ cartons: newValue })
                });
                
                const data = await res.json();
                
                if (data.success) {
                    updateItemUI(data);
                    flashRow(itemId, true);
                    playBeep(true);
                    inputElement.defaultValue = newValue; // Actualiza el valor por defecto
                } else {
                    flashRow(itemId, false);
                    playBeep(false);
                    inputElement.value = inputElement.defaultValue; // Revertir si hay error
                }
            } catch (err) {
                playBeep(false);
                inputElement.value = inputElement.defaultValue;
            } finally {
                inputElement.disabled = false;
            }
        }


        /* ── Abrir modal de nota ────────────────────────────────────── */
        function openNoteModal(itemId, label, currentNotes, markNotReceived) {
            window.dispatchEvent(new CustomEvent('open-note-modal', {
                detail: { itemId, itemLabel: label, notes: currentNotes || '', markNotReceived: !!markNotReceived }
            }));
        }

        /* ── Alpine: Scanner ────────────────────────────────────────── */
        function scannerModule() {
            return {
                barcode: '',
                isReady: true,
                isProcessing: false,
                lastScan: null,
                scanLog: [],

                init() {
                    this.$nextTick(() => this.$refs.scanInput?.focus());
                    document.addEventListener('click', (e) => {
                        if (['INPUT', 'TEXTAREA', 'SELECT', 'BUTTON', 'A'].includes(e.target.tagName)) return;
                        if (e.target.closest('[x-data="noteModal()"]')) return;
                        this.$refs.scanInput?.focus();
                    });
                },

                async scan() {
                    const code = this.barcode.trim();
                    if (!code || this.isProcessing) return;

                    this.isReady = false;
                    this.isProcessing = true;

                    try {
                        const res = await fetch(SCAN_URL, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                            body: JSON.stringify({ barcode: code }),
                        });
                        const data = await res.json();
                        const time = new Date().toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                        if (data.success) {
                            playBeep(true);
                            flashRow(data.item_id, true);
                            updateItemUI(data);
                            this.lastScan = {
                                success: true,
                                message: `✓ Caja registrada — ${data.product_description}`,
                                detail: `Cajas: ${data.received_cartons} de ${data.carton_count}`,
                            };
                            this.scanLog.unshift({ barcode: code, success: true, time });
                        } else {
                            playBeep(false);
                            this.lastScan = { success: false, message: data.message || `No encontrado: ${code}` };
                            this.scanLog.unshift({ barcode: code, success: false, time });
                        }
                    } catch (err) {
                        playBeep(false);
                        this.lastScan = { success: false, message: 'Error de conexión al servidor' };
                    }

                    // Máximo 10 entradas en el log
                    if (this.scanLog.length > 10) this.scanLog = this.scanLog.slice(0, 10);

                    this.barcode = '';
                    this.isProcessing = false;
                    this.isReady = true;
                    this.$nextTick(() => this.$refs.scanInput?.focus());
                },
            };
        }

        /* ── Alpine: Modal de notas ─────────────────────────────────── */
        function noteModal() {
            return {
                open: false,
                itemId: null,
                itemLabel: '',
                notes: '',
                reason: '',
                saving: false,
                feedback: '',
                feedbackOk: false,
                isMarkNotReceived: false,

                openModal(detail) {
                    this.itemId            = detail.itemId;
                    this.itemLabel         = detail.itemLabel;
                    this.notes             = detail.notes || '';
                    this.reason            = '';
                    this.feedback          = '';
                    this.feedbackOk        = false;
                    this.isMarkNotReceived = detail.markNotReceived || false;
                    this.open              = true;

                    if (this.isMarkNotReceived && !this.notes) {
                        this.reason = 'Producto no embarcado';
                        this.notes  = 'Producto no embarcado';
                    }
                },

                close() { this.open = false; },

                async save() {
                    if (!this.notes.trim()) {
                        this.feedback = 'Ingrese una nota o seleccione una razón.';
                        this.feedbackOk = false;
                        return;
                    }

                    this.saving = true;
                    this.feedback = '';

                    try {
                        const action = this.isMarkNotReceived ? 'mark-not-received' : 'update-notes';
                        const res = await fetch(`${ITEMS_BASE}/${this.itemId}/${action}`, {
                            method: 'PATCH',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                            body: JSON.stringify({ notes: this.notes.trim() }),
                        });

                        const data = await res.json();

                        if (data.success) {
                            this.feedback = this.isMarkNotReceived ? 'Artículo marcado como no recibido.' : 'Nota guardada.';
                            this.feedbackOk = true;

                            if (data.item_id) updateItemUI(data);
                            updateNoteRow(this.itemId, this.notes.trim());

                            setTimeout(() => this.close(), 800);
                        } else {
                            this.feedback = data.message || 'Error al guardar.';
                            this.feedbackOk = false;
                        }
                    } catch (err) {
                        this.feedback = 'Error de conexión al servidor.';
                        this.feedbackOk = false;
                    }

                    this.saving = false;
                },
                async clearNote() {
                    this.saving = true;
                    this.feedback = '';
                    
                    try {
                        const res = await fetch(`${ITEMS_BASE}/${this.itemId}/update-notes`, {
                            method: 'PATCH',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                            body: JSON.stringify({ notes: '' }), 
                        });

                        const data = await res.json();

                        if (data.success) {
                            this.feedback = 'Nota eliminada. Estado revertido.';
                            this.feedbackOk = true;

                            if (data.item_id) updateItemUI(data);
                            updateNoteRow(this.itemId, '');

                            setTimeout(() => this.close(), 800);
                        } else {
                            this.feedback = data.message || 'Error al eliminar.';
                            this.feedbackOk = false;
                        }
                    } catch (err) {
                        this.feedback = 'Error de conexión.';
                        this.feedbackOk = false;
                    }

                    this.saving = false;
                },
            };
        }
    </script>
    @endpush
</x-app-layout>