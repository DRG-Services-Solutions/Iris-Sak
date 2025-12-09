<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>DRG Iris - Sistema de Gestión De Activos</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Tu CSS de Tailwind existente aquí */
            </style>
        @endif

        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .animate-fade-in-up {
                animation: fadeInUp 0.6s ease-out forwards;
            }
            .animate-delay-100 { animation-delay: 0.1s; opacity: 0; }
            .animate-delay-200 { animation-delay: 0.2s; opacity: 0; }
            .animate-delay-300 { animation-delay: 0.3s; opacity: 0; }
            .animate-delay-400 { animation-delay: 0.4s; opacity: 0; }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-100 to-gray-200 dark:from-gray-900 dark:via-slate-900 dark:to-gray-800">
            
            {{-- Elementos decorativos de fondo --}}
            <div class="fixed inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-float"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-slate-500/10 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
            </div>

            {{-- Navbar --}}
            <nav class="relative z-10 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        {{-- Logo --}}
                        <div class="flex items-center space-x-3">
                            <div class="bg-gradient-to-br from-slate-700 to-slate-900 p-2 rounded-lg shadow-lg">
                                
                                <x-application-logo/>
                                
                            </div>
                            <div>
                                <!--
                                <h1 class="text-xl font-bold text-gray-900 dark:text-white">DRG</h1>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Gestión Industrial</p>
                                -->
                            </div>
                        </div>

                        {{-- Botones de navegación --}}
                        @if (Route::has('login'))
                            <div class="flex items-center space-x-3">
                                @guest
                                    <a href="{{ route('login') }}"
                                       class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                                        Iniciar Sesión
                                    </a>
                                    <!--
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}"
                                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-slate-700 to-slate-900 hover:from-slate-600 hover:to-slate-800 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                            Registrarse
                                        </a>
                                    @endif
                                    -->
                                @else
                                    <a href="{{ route('dashboard') }}"
                                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-slate-700 to-slate-900 hover:from-slate-600 hover:to-slate-800 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                        Dashboard
                                    </a>
                                @endguest
                            </div>
                        @endif
                    </div>
                </div>
            </nav>

            {{-- Hero Section --}}
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    
                    {{-- Contenido principal --}}
                    <div class="text-center lg:text-left">
                        <div class="animate-fade-in-up">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-300 dark:border-blue-700 mb-6">
                                <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Sistema de Gestión Industrial
                            </span>
                        </div>

                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6 animate-fade-in-up animate-delay-100">
                            Control Total de tu 
                            <span class="bg-gradient-to-r from-blue-600 to-slate-600 bg-clip-text text-transparent">
                                Almacen
                            </span>
                        </h1>

                        <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 mb-8 leading-relaxed animate-fade-in-up animate-delay-200">
                            Gestiona productos, inventarios con RFID y trazabilidad de productos con la mejor tecnología.
                        </p>

                        @if (Route::has('login'))
                            <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 animate-fade-in-up animate-delay-300">
                                @guest
                                    <a href="{{ route('login') }}"
                                       class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-slate-700 to-slate-900 hover:from-slate-600 hover:to-slate-800 text-white font-bold text-base rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                        Iniciar Sesión
                                    </a>
                                    <!--
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}"
                                           class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold text-base rounded-xl border-2 border-gray-300 dark:border-gray-600 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                            </svg>
                                            Crear Cuenta
                                        </a>
                                    @endif
                                    -->
                                @else
                                    <a href="{{ route('dashboard') }}"
                                       class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-slate-700 to-slate-900 hover:from-slate-600 hover:to-slate-800 text-white font-bold text-base rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                        Ir al Dashboard
                                    </a>
                                @endguest
                            </div>
                        @endif
                    </div>

                    {{-- Imagen/Logo destacado --}}
                    <div class="flex items-center justify-center animate-fade-in-up animate-delay-400">
                        <div class="relative">
                            {{-- Círculo decorativo --}}
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-400/20 to-slate-600/20 rounded-full blur-3xl"></div>
                            
                            {{-- Logo principal --}}
                            <div class="relative bg-black dark:bg-gray-800 rounded-3xl shadow-2xl p-12 border border-gray-200 dark:border-gray-700">
                                <img src="{{ asset('images/drg-logo.webp') }}" 
                                     alt="Logo Kone" 
                                     class="w-64 h-auto mx-auto">
                            </div>

                            {{-- Badges flotantes --}}
                            <div class="absolute -top-4 -right-4 bg-green-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg animate-float">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Activo</span>
                                </div>
                            </div>

                            <div class="absolute -bottom-4 -left-4 bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg animate-float" style="animation-delay: 1s;">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                    <span>RFID</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Features Grid --}}
                <div class="mt-24 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-700 w-12 h-12 rounded-lg flex items-center justify-center mb-4 shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Órdenes de Trabajo</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Gestiona y supervisa todas tus órdenes de producción en tiempo real.</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300">
                        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 w-12 h-12 rounded-lg flex items-center justify-center mb-4 shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Trazabilidad RFID</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Control total con tecnología RFID para tus herramientas y componentes.</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300">
                        <div class="bg-gradient-to-br from-amber-500 to-amber-700 w-12 h-12 rounded-lg flex items-center justify-center mb-4 shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Inventario Inteligente</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Administra tu catálogo de productos con control de stock automatizado.</p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <footer class="relative z-10 bg-slate-700 dark:bg-slate-900 border-t border-slate-600 dark:border-slate-800 mt-24">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div class="flex items-center space-x-3 mb-4 md:mb-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-white">
                                Sistema de gestión de inventario y control de almacen
                            </p>
                        </div>
                        <div class="text-sm text-gray-300">
                            © {{ date('Y') }} DRG Services & Solutions. Todos los derechos reservados.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>