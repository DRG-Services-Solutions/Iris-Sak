<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-teal-600 to-teal-800 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-ship text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        Recepción de Contenedores
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Registro y control de ingreso de mercancía</p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <div class="text-center px-4 py-2 bg-slate-700 rounded-lg">
                    <p class="text-2xl font-bold text-white">{{ $containers->total() }}</p>
                    <p class="text-xs text-gray-300">Contenedores</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes flash --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            {{-- Barra de acciones --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <form method="GET" action="{{ route('containers.index') }}" class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                    <div class="flex-1 max-w-lg">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                   placeholder="Buscar por número de contenedor, proveedor...">
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <select name="customs_status" class="border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm py-2.5 px-3 focus:ring-2 focus:ring-teal-500">
                            <option value="">Todos los estatus</option>
                            <option value="pendiente" {{ request('customs_status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_revision" {{ request('customs_status') === 'en_revision' ? 'selected' : '' }}>En revisión</option>
                            <option value="liberado" {{ request('customs_status') === 'liberado' ? 'selected' : '' }}>Liberado</option>
                            <option value="retenido" {{ request('customs_status') === 'retenido' ? 'selected' : '' }}>Retenido</option>
                        </select>
                        <button type="submit" class="px-4 py-2.5 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition text-sm">
                            <i class="fas fa-filter mr-1"></i> Filtrar
                        </button>
                        <a href="{{ route('containers.create') }}"
                           class="px-4 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-500 transition text-sm font-medium">
                            <i class="fas fa-plus mr-1"></i> Nuevo Contenedor
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabla de contenedores --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contenedor</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Declarado / Recibido</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aduana</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estatus</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($containers as $container)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $container->container_seal_number }}</span>
                                    </td>
                                   
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm font-medium {{ $container->received_qty === $container->declared_qty ? 'text-green-600' : 'text-amber-600' }}">
                                            {{ number_format($container->received_qty) }} / {{ number_format($container->declared_qty) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $customsBadge = match($container->customs_status) {
                                                'pendiente'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                'en_revision' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                'liberado'    => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                'retenido'    => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                default       => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $customsBadge }}">
                                            {{ ucfirst(str_replace('_', ' ', $container->customs_status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $statusBadge = match($container->status) {
                                                'abierto'    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                'en_proceso' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                                'cerrado'    => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                default      => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">
                                            {{ ucfirst($container->status) }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $container->received_at?->format('d/m/Y H:i') ?? $container->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('containers.show', $container) }}" class="text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white" title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('containers.inspection', $container) }}" class="text-teal-600 hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-200" title="Etiquetado / Inspección">
                                                <i class="fas fa-tags"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-ship text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">No hay contenedores registrados</p>
                                            <a href="{{ route('containers.create') }}" class="mt-3 text-teal-600 hover:text-teal-500 text-sm font-medium">
                                                <i class="fas fa-plus mr-1"></i> Registrar primer contenedor
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($containers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $containers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
