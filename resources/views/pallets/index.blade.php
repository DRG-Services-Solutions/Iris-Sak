<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-teal-600 to-teal-800 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-pallet text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        Gestión de Pallets
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Control de tarimas y asignación de ubicaciones</p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <div class="text-center px-4 py-2 bg-slate-700 rounded-lg">
                    <p class="text-2xl font-bold text-white">{{ $pallets->count() }}</p>
                    <p class="text-xs text-gray-300">Total Pallets</p>
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

            {{-- Barra de acciones --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                    <div class="flex-1 max-w-lg">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" placeholder="Buscar pallet o contenedor..." 
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>
                    </div>

                    
                </div>
            </div>

            {{-- Tabla de Pallets --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Código / Tarima</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contenedor</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ubicación Asignada</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Asignacion de Ubicacion</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($pallets as $pallet)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $pallet->pallet_code }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        <div class="flex items-center">
                                            <i class="fas fa-box text-xs mr-2 text-gray-400"></i>
                                            {{ $pallet->container ? $pallet->container->container_seal_number : 'Sin asignar' }} -- {{ $pallet->container ? $pallet->container->container_number : 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                            <i class="fas fa-map-marker-alt mr-1 text-teal-500"></i>
                                            {{ $pallet->location ? $pallet->location->name : 'Pendiente' }}
                                        </span>
                                    </td>
                                    
                                    @if($pallet->hasLocation())
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-300 text-right">
                                                {{ $pallet->location->code }} - {{ $pallet->location->name }}
                                            </span>
                                        </td>
                                    @else
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form action="{{ route('pallets.assign-location', $pallet) }}" method="POST" class="flex items-center space-x-2">
                                            @csrf
                                            <select name="location_id" class="text-xs border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-teal-500 py-1">
                                                <option class="text-gray-500 text-center" value="">Seleccionar...</option>
                                                @foreach($locations as $location)
                                                    @if ($location->hasPallets())
                                                        <option value="{{ $location->id }}" disabled class="text-red-500">
                                                            {{ $location->code }} - {{ $location->name }} (Ocupada)
                                                        </option>
                                                    
                                                    @else
                                                    <option value="{{ $location->id }}" {{ $pallet->location_id == $location->id ? 'selected' : '' }}>
                                                        {{ $location->code }} - {{ $location->name }} -- {{ $location->hasPallets() ? 'Ocupada' : 'Disponible' }} 
                                                    </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <button type="submit" class="p-1.5 bg-slate-700 text-white rounded hover:bg-slate-600 transition" title="Guardar ubicación">
                                                <i class="fas fa-check text-xs"></i>
                                            </button>
                                           
                                        </form>
                                    </td>

                                    @endif
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center space-x-3">
                                            <a href="{{ route('pallets.show', $pallet) }}" class="text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white" title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('pallets.edit', $pallet) }}" class="text-teal-600 hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-200" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('pallets.destroy', $pallet) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar esta tarima?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-pallet text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">No se encontraron pallets</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>