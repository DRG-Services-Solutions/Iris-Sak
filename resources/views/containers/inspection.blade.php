<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('containers.show', $container) }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div class="bg-gradient-to-br from-amber-500 to-amber-700 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-tags text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">Etiquetado Inicial — Aduana</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Contenedor {{ $container->container_number }} · {{ $container->supplier ?? '' }}</p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-3">
                @php
                    $customsBadge = match($container->customs_status) {
                        'pendiente'   => 'bg-yellow-100 text-yellow-800',
                        'en_revision' => 'bg-blue-100 text-blue-800',
                        'liberado'    => 'bg-green-100 text-green-800',
                        'retenido'    => 'bg-red-100 text-red-800',
                        default       => 'bg-gray-100 text-gray-800',
                    };
                @endphp
                <span class="inline-flex px-3 py-1.5 rounded-full text-sm font-medium {{ $customsBadge }}">
                    <i class="fas fa-passport mr-1"></i> {{ ucfirst(str_replace('_', ' ', $container->customs_status)) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="inspectionManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Mensajes flash --}}
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

            {{-- Resumen rápido --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-indigo-600">{{ $container->inspectionLabels->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total etiquetas</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $container->inspectionLabels->where('inspection_status', 'conforme')->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Conformes</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-600">{{ $container->inspectionLabels->where('inspection_status', 'pendiente')->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pendientes</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">{{ $container->inspectionLabels->where('inspection_status', 'con_diferencia')->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Con diferencia</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-cyan-600">{{ $container->inspectionLabels->where('printed', true)->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Impresas</p>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- Todo lo operativo: solo si el contenedor está LIBERADO      --}}
            {{-- ============================================================ --}}
            @if($container->customs_status === 'liberado')

            {{-- Generar etiquetas --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-print text-amber-500 mr-2"></i>Generar Etiquetas de Inspección
                </h3>
                <form method="POST" action="{{ route('containers.generate-labels', $container) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Artículo del Packing List</label>
                        <select name="container_item_id" required
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2.5 px-3 focus:ring-2 focus:ring-amber-500">
                            <option value="">Seleccionar artículo...</option>
                            @foreach($container->items as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->product_code ? $item->product_code . ' — ' : '' }}{{ $item->product_description }} ({{ $item->declared_qty }} pzas)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Cantidad de etiquetas</label>
                        <input type="number" name="quantity" required min="1" max="5000" value="1"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2.5 px-3 focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <button type="submit" class="w-full px-4 py-2.5 bg-amber-600 text-white rounded-lg hover:bg-amber-500 transition text-sm font-medium">
                            <i class="fas fa-barcode mr-1"></i> Generar Etiquetas
                        </button>
                    </div>
                </form>
            </div>

            {{-- Acciones masivas --}}
            @if($container->inspectionLabels->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" @click="toggleAll($event)" class="rounded border-gray-300 dark:border-gray-600 text-teal-600 focus:ring-teal-500">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Seleccionar todo</span>
                        <span class="text-sm text-gray-400 ml-2" x-show="selectedLabels.length > 0" x-text="selectedLabels.length + ' seleccionadas'"></span>
                    </div>
                    <div class="flex items-center space-x-2" x-show="selectedLabels.length > 0" x-cloak>
                        <form method="POST" action="{{ route('containers.bulk-inspect', $container) }}" x-ref="bulkForm">
                            @csrf
                            <template x-for="id in selectedLabels" :key="id">
                                <input type="hidden" name="label_ids[]" :value="id">
                            </template>
                            <div class="flex items-center space-x-2">
                                <select name="inspection_status" class="border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm py-2 px-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500">
                                    <option value="conforme">Conforme</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="con_diferencia">Con diferencia</option>
                                </select>
                                <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-500 text-sm transition">
                                    <i class="fas fa-check-double mr-1"></i> Aplicar
                                </button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('containers.mark-printed', $container) }}">
                            @csrf
                            <template x-for="id in selectedLabels" :key="id">
                                <input type="hidden" name="label_ids[]" :value="id">
                            </template>
                            <button type="submit" class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-500 text-sm transition">
                                <i class="fas fa-print mr-1"></i> Marcar Impresas
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Tabla de etiquetas --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                        <i class="fas fa-tag text-amber-500 mr-2"></i>Etiquetas Generadas
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 w-10"></th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Código</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Artículo</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Pieza #</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Estatus</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Inspector</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Impresa</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($container->inspectionLabels->sortBy('piece_number') as $label)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" :value="{{ $label->id }}" x-model.number="selectedLabels"
                                               class="rounded border-gray-300 dark:border-gray-600 text-teal-600 focus:ring-teal-500">
                                    </td>
                                    <td class="px-4 py-3 text-sm font-mono font-bold text-gray-900 dark:text-white">{{ $label->label_code }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $label->containerItem?->product_description ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center text-sm font-medium text-gray-800 dark:text-gray-200">{{ $label->piece_number }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $lBadge = match($label->inspection_status) {
                                                'conforme'       => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                'con_diferencia' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                default          => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $lBadge }}">
                                            {{ ucfirst(str_replace('_', ' ', $label->inspection_status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $label->inspector?->name ?? '—' }}
                                        @if($label->inspected_at)
                                            <span class="text-xs text-gray-400 block">{{ $label->inspected_at->format('d/m H:i') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($label->printed)
                                            <i class="fas fa-check-circle text-cyan-500"></i>
                                        @else
                                            <i class="fas fa-circle text-gray-300 dark:text-gray-600"></i>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <form method="POST" action="{{ route('containers.update-label', $label) }}" class="inline-flex items-center space-x-1">
                                            @csrf @method('PATCH')
                                            <select name="inspection_status" class="border border-gray-300 dark:border-gray-600 rounded text-xs py-1 px-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500">
                                                <option value="conforme" {{ $label->inspection_status === 'conforme' ? 'selected' : '' }}>Conforme</option>
                                                <option value="pendiente" {{ $label->inspection_status === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                <option value="con_diferencia" {{ $label->inspection_status === 'con_diferencia' ? 'selected' : '' }}>Con diferencia</option>
                                            </select>
                                            <button type="submit" class="text-amber-600 hover:text-amber-800 dark:text-amber-400" title="Actualizar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <i class="fas fa-tags text-4xl text-gray-300 dark:text-gray-600 mb-3 block"></i>
                                        <p class="text-gray-500 dark:text-gray-400 font-medium">No se han generado etiquetas aún</p>
                                        <p class="text-sm text-gray-400 mt-1">Seleccione un artículo y genere etiquetas arriba</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @else
            {{-- ============================================================ --}}
            {{-- BLOQUEO: contenedor NO liberado                              --}}
            {{-- ============================================================ --}}
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-8 text-center">
                <i class="fas fa-lock text-4xl text-amber-400 mb-4 block"></i>
                <p class="text-lg text-amber-800 dark:text-amber-200 font-semibold">Contenedor aún no liberado por aduana</p>
                <p class="text-sm text-amber-600 dark:text-amber-400 mt-2 max-w-md mx-auto">
                    Los artículos del Packing List y la generación de etiquetas estarán disponibles una vez que el estatus de aduana sea <span class="font-bold">Liberado</span>.
                </p>
                <p class="text-xs text-amber-500 mt-3">
                    Estatus actual: 
                    <span class="inline-flex ml-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $customsBadge }}">
                        {{ ucfirst(str_replace('_', ' ', $container->customs_status)) }}
                    </span>
                </p>
                <a href="{{ route('containers.show', $container) }}" class="inline-flex items-center mt-4 text-sm text-amber-700 dark:text-amber-300 hover:underline">
                    <i class="fas fa-arrow-left mr-1"></i> Volver al detalle del contenedor para cambiar estatus
                </a>
            </div>
            @endif

        </div>
    </div>

    <script>
        function inspectionManager() {
            return {
                selectedLabels: [],
                toggleAll(event) {
                    if (event.target.checked) {
                        this.selectedLabels = @json($container->inspectionLabels->pluck('id'));
                    } else {
                        this.selectedLabels = [];
                    }
                }
            }
        }
    </script>
</x-app-layout>
