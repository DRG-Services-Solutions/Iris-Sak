<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-purple-600 to-purple-800 p-3 rounded-lg shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                        {{ __('Mi Perfil') }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Administra tu información personal y configuración de seguridad</p>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" 
               class="hidden md:inline-flex items-center px-4 py-2 bg-slate-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-200 hover:bg-slate-200 dark:hover:bg-gray-600 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Información del Usuario --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Información Personal</h3>
                            <p class="text-sm text-purple-100 mt-0.5">Actualiza tu nombre y correo electrónico</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            {{-- Actualizar Contraseña --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Seguridad de la Cuenta</h3>
                            <p class="text-sm text-blue-100 mt-0.5">Cambia tu contraseña para mantener tu cuenta segura</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    {{-- Recomendaciones de Seguridad --}}
                    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-2">
                                    Recomendaciones de Seguridad
                                </h4>
                                <ul class="text-sm text-blue-700 dark:text-blue-400 space-y-1 list-disc list-inside">
                                    <li>Usa al menos 8 caracteres</li>
                                    <li>Combina letras mayúsculas y minúsculas</li>
                                    <li>Incluye números y símbolos</li>
                                    <li>No uses información personal obvia</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            {{-- Eliminar Cuenta --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border-2 border-red-200 dark:border-red-900">
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Zona de Peligro</h3>
                            <p class="text-sm text-red-100 mt-0.5">Elimina permanentemente tu cuenta y todos tus datos</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    {{-- Advertencia Importante --}}
                    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2">
                                    ⚠️ Advertencia: Esta acción es irreversible
                                </h4>
                                <p class="text-sm text-red-700 dark:text-red-400">
                                    Al eliminar tu cuenta, perderás permanentemente:
                                </p>
                                <ul class="mt-2 text-sm text-red-700 dark:text-red-400 space-y-1 list-disc list-inside">
                                    <li>Todos tus datos personales</li>
                                    <li>Historial de órdenes de trabajo</li>
                                    <li>Acceso a todos los recursos del sistema</li>
                                    <li>Configuraciones y preferencias</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

            {{-- Información Adicional --}}
            <div class="bg-slate-50 dark:bg-slate-900/50 rounded-lg p-5 border border-slate-200 dark:border-slate-700">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-2">
                            Información de Privacidad
                        </h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Tus datos están protegidos y encriptados. Solo tú puedes modificar tu información personal. 
                            El sistema mantiene un registro de actividad para garantizar la seguridad de tu cuenta.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <x-industrial-footer>
                Sistema de gestión de inventario industrial - Configuración de perfil de usuario
            </x-industrial-footer>

        </div>
    </div>
</x-app-layout>