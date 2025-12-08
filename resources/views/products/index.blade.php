<x-app-layout>
    {{-- Slot para el encabezado de la página --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lista de Herramientas') }}
        </h2>
    </x-slot>

    {{-- Contenido principal de la página --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Aquí pondremos la tabla para listar las herramientas --}}
                    <h3 class="text-lg font-medium mb-4">Herramientas Registradas</h3>

                    {{-- Botón para ir a crear (lo implementaremos después) --}}
                    <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-white-400 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mb-4">
                        Añadir Herramienta
                    </a>

                    {{-- Tabla (adaptar columnas a tu migración) --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Código de Barras</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descripción</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                    
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                {{-- Iteramos sobre las herramientas pasadas desde el controlador --}}
                                @forelse ($products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $product->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $product->barcode }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $product->description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                            {{-- Enlaces para acciones --}}
                                            <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Ver</a>
                                            <a href="{{ route('products.edit', $product) }}" class="text-white-600 rounded-md hover:text-blue-900 mr-2">Editar</a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline"> @csrf @method('DELETE') <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Eliminar?')">Eliminar</button> </form>
                                        </td>
                                    </tr>
                                @empty
                                    {{-- Mensaje si no hay herramientas --}}
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No hay herramientas registradas todavía.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     {{-- Si usaste paginate() en el controlador, aquí irían los enlaces de paginación: {{ $products->links() }} --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>