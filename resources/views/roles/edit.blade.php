{{-- resources/views/roles/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-3 rounded-lg shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    Editar Rol: {{ $role->name }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Modifica el nombre o ajusta los permisos de este perfil</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Sección 1: Nombre del Rol --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
                    <div class="h-2 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-700"></div>
                    <div class="p-6 md:p-8">
                        <div>
                            <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nombre del Rol <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                                   class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition duration-200">
                            @error('name') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Sección 2: Configuración de Permisos --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">Ajustar permisos del rol</h3>
                        @error('permissions') <p class="mt-2 text-sm text-red-500 font-bold">Debes seleccionar al menos un permiso.</p> @enderror
                    </div>

                    <div class="p-6 md:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($permissions as $module => $modulePermissions)
                                <div class="bg-gray-100 dark:bg-gray-900/50 rounded-lg p-5 border border-gray-200 dark:border-gray-700 h-full shadow-inner">
                                    <h4 class="font-bold text-emerald-700 dark:text-emerald-400 mb-4 border-b border-gray-300 dark:border-gray-600 pb-2">
                                        {{ $module ?: 'Permisos Generales' }}
                                    </h4>
                                    
                                    <div class="space-y-4">
                                        @foreach($modulePermissions as $permission)
                                            <label class="flex items-center cursor-pointer group">
                                                <div class="flex items-center h-5">
                                                    {{-- Lógica para marcar el checkbox si el rol ya lo tiene --}}
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                                           {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                           class="focus:ring-emerald-500 h-5 w-5 text-emerald-600 border-gray-400 dark:border-gray-600 dark:bg-gray-800 rounded transition-colors cursor-pointer">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <span class="font-bold text-gray-800 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                                        {{ $permission->display_name }}
                                                    </span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="mt-6 flex items-center justify-end space-x-4">
                    <a href="{{ route('roles.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">Cancelar</a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-600 border border-transparent rounded-lg font-bold text-sm text-white shadow-lg hover:from-amber-700 hover:to-orange-700 transform hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Actualizar Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>