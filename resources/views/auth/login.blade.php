<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 via-slate-100 to-gray-200 dark:from-gray-900 dark:via-slate-900 dark:to-gray-800 py-2 px-4 sm:px-6 lg:px-8">
        
        {{-- Elementos decorativos de fondo --}}
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-slate-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-md w-full space-y-8">
            
            {{-- Header --}}
            <div class="text-center">
                <div class="flex items-center justify-center mb-6">
                    <div class="bg-gradient-to-br from-slate-700 to-slate-900 p-4 rounded-2xl shadow-2xl">
                        <x-application-logo class="w-auto h-24 fill-current text-gray-500" />

                    </div>
                </div>
              
            </div>

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- Card del formulario --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                {{-- Header del formulario --}}
                <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-2">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-white">Credenciales de Acceso</h3>
                    </div>
                </div>

                {{-- Formulario --}}
                <form method="POST" action="{{ route('login') }}" class="p-6 space-y-6">
                    @csrf

                    {{-- Email Address --}}
                    <div>
                        <x-input-label for="email" :value="__('Correo Electrónico')" class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <x-text-input 
                                id="email" 
                                class="pl-10 block w-full border-gray-300 dark:border-gray-600 focus:border-slate-500 focus:ring-slate-500 rounded-lg shadow-sm" 
                                type="email" 
                                name="email" 
                                :value="old('email')" 
                                required 
                                autofocus 
                                autocomplete="username"
                                placeholder="tu@email.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" :value="__('Contraseña')" class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <x-text-input 
                                id="password" 
                                class="pl-10 block w-full border-gray-300 dark:border-gray-600 focus:border-slate-500 focus:ring-slate-500 rounded-lg shadow-sm"
                                type="password"
                                name="password"
                                required 
                                autocomplete="current-password"
                                placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                            <input 
                                id="remember_me" 
                                type="checkbox" 
                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-slate-600 shadow-sm focus:ring-slate-500 dark:focus:ring-slate-600 dark:focus:ring-offset-gray-800 cursor-pointer" 
                                name="remember">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-100 transition-colors duration-200">
                                {{ __('Recordarme') }}
                            </span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 font-medium transition-colors duration-200" 
                               href="{{ route('password.request') }}">
                                {{ __('¿Olvidaste tu contraseña?') }}
                            </a>
                        @endif
                    </div>

                    {{-- Separador --}}
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>

                    {{-- Botón de Login --}}
                    <div class="space-y-4">
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-slate-700 to-slate-900 hover:from-slate-600 hover:to-slate-800 text-white font-bold text-base rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            {{ __('Iniciar Sesión') }}
                        </button>

                        {{-- Link a registro --}}
                        @if (Route::has('register'))
                            <div class="text-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    ¿No tienes cuenta?
                                </span>
                                <a href="{{ route('register') }}" 
                                   class="text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 ml-1 transition-colors duration-200">
                                    Regístrate aquí
                                </a>
                            </div>
                        @endif
                    </div>
                </form>

            </div>

            

            {{-- Advertencia de seguridad --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Nunca compartas tus credenciales de acceso. El sistema registra todos los inicios de sesión.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>