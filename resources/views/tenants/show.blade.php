{{-- resources/views/tenants/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-3 rounded-lg shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        Detalles del Cliente
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Información general y usuarios de la empresa</p>
                </div>
            </div>
            
            {{-- Botón para regresar --}}
            <a href="{{ route('tenants.index') }}" class="hidden md:inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al Listado
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- SECCIÓN 1: Tarjeta de Información de la Empresa --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-blue-700"></div>
                <div class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div class="flex items-center mb-4 md:mb-0">
                            <div class="flex-shrink-0 h-16 w-16 bg-gradient-to-br from-slate-600 to-slate-800 rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-white text-2xl font-bold">{{ substr($tenant->name, 0, 1) }}</span>
                            </div>
                            <div class="ml-6">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $tenant->name }}</h3>
                                <div class="flex items-center mt-2 space-x-4">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">ID: #{{ str_pad($tenant->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Registrado el: {{ $tenant->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            @if($tenant->is_active)
                                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                    Cuenta Activa
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                    Cuenta Inactiva
                                </span>
                            @endif
                            
                            {{-- Botón Editar desde la vista Show --}}
                            <a href="{{ route('tenants.edit', $tenant) }}" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: Lista de Usuarios del Cliente --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Usuarios Asignados ({{ $tenant->users->count() }})
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Correo Electrónico</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha de Registro</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($tenant->users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center">
                                                <span class="text-slate-600 dark:text-slate-300 font-bold text-xs">{{ substr($user->name, 0, 2) }}</span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $user->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Esta empresa aún no tiene usuarios registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Botón volver en móvil --}}
            <div class="md:hidden mt-6">
                <a href="{{ route('tenants.index') }}" class="flex justify-center items-center w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al Listado
                </a>
            </div>

        </div>
    </div>
</x-app-layout>