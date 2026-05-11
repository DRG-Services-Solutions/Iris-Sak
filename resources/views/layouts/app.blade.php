<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        {{-- Enterprise Browser Scripts --}}
        <script src="/ebapi-modules.js" type="text/javascript" charset="utf-8"></script>
        <script src="/elements.js" type="text/javascript" charset="utf-8"></script>
        
        <title>{{ config('app.name', 'Iris') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            @keyframes float-slow {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
            .animate-float-slow { animation: float-slow 8s ease-in-out infinite; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100"
          x-data="sidebarState()" 
          x-init="init()"
          @keydown.escape.window="pinned = false">
        
        <div class="h-screen overflow-hidden bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100 dark:from-gray-900 dark:via-slate-900 dark:to-gray-800">
            
            {{-- Decorativos de fondo --}}
            <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
                <div class="absolute top-0 right-0 w-96 h-96 bg-slate-500/5 dark:bg-slate-500/10 rounded-full blur-3xl animate-float-slow"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500/5 dark:bg-blue-500/10 rounded-full blur-3xl animate-float-slow" style="animation-delay: 2s;"></div>
            </div>

            {{-- BACKDROP: solo en móvil cuando el sidebar está abierto --}}
            <div x-show="mobileOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="mobileOpen = false"
                 class="fixed inset-0 bg-black/50 z-30 lg:hidden"
                 x-cloak>
            </div>

            {{-- SIDEBAR --}}
            @include('layouts.sidebar')

            {{-- CONTENIDO PRINCIPAL --}}
            <div class="relative z-10 flex flex-col h-full overflow-y-auto overflow-x-hidden transition-all duration-300"
                 :class="isMobile ? 'ml-0' : (expanded ? 'ml-72' : 'ml-[68px]')">
                
                {{-- Header --}}
                <header class="sticky top-0 z-20 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md shadow-sm border-b border-gray-200 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            {{-- Hamburguesa: solo visible en móvil --}}
                            <button @click="mobileOpen = !mobileOpen" 
                                    class="lg:hidden flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    title="Menú">
                                <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            {{-- Contenido del header de cada página --}}
                            <div class="flex-1 min-w-0">
                                @isset($header)
                                    {{ $header }}
                                @else
                                    <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100">{{ config('app.name', 'Iris') }}</h2>
                                @endisset
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Page Content --}}
                <main class="flex-1 w-full">
                    {{ $slot }}
                </main>

                {{-- Footer --}}
                <footer class="mt-auto py-6 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border-t border-gray-200 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex flex-col md:flex-row items-center justify-between text-center md:text-left">
                            <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>{{ config('app.name', 'DRG - Iris') }}</span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-500">
                                &copy; {{ date('Y') }} Sistema de Gestión Industrial. Todos los derechos reservados.
                            </div>
                        </div>
                    </div>
                </footer>
            </div>

        </div>

        <script>
            function sidebarState() {
                return {
                    // Desktop: hover + pin
                    pinned: false,
                    hovered: false,
                    // Mobile: drawer tradicional
                    mobileOpen: false,
                    isMobile: window.innerWidth < 1024,

                    get expanded() {
                        return this.pinned || this.hovered;
                    },

                    init() {
                        // Restaurar preferencia de pin en desktop
                        if (!this.isMobile) {
                            this.pinned = localStorage.getItem('iris_sidebar_pinned') === 'true';
                        }

                        // Listener de resize para cambiar entre modos
                        window.addEventListener('resize', () => {
                            this.isMobile = window.innerWidth < 1024;
                            if (!this.isMobile) {
                                this.mobileOpen = false;
                            }
                        });
                    },

                    togglePin() {
                        this.pinned = !this.pinned;
                        localStorage.setItem('iris_sidebar_pinned', this.pinned);
                    },

                    sidebarEnter() {
                        if (!this.isMobile) this.hovered = true;
                    },

                    sidebarLeave() {
                        if (!this.isMobile) this.hovered = false;
                    },
                }
            }
        </script>

        @stack('scripts')
    </body>
</html>