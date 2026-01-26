<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        {{-- Enterprise Browser Scripts --}}
        <script src="/ebapi-modules.js" type="text/javascript" charset="utf-8"></script>
        <script src="/elements.js" type="text/javascript" charset="utf-8"></script>
        
        <title>{{ config('app.name', 'Kone App') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Iconos Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Animaciones sutiles para elementos de fondo */
            @keyframes float-slow {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
            .animate-float-slow {
                animation: float-slow 8s ease-in-out infinite;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100 dark:from-gray-900 dark:via-slate-900 dark:to-gray-800">
            
            {{-- Elementos decorativos de fondo sutiles --}}
            <div class="fixed inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-0 right-0 w-96 h-96 bg-slate-500/5 dark:bg-slate-500/10 rounded-full blur-3xl animate-float-slow"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500/5 dark:bg-blue-500/10 rounded-full blur-3xl animate-float-slow" style="animation-delay: 2s;"></div>
            </div>

            {{-- Navegación --}}
                @include('layouts.navigation')
            

            {{-- Page Heading --}}
            @isset($header)
                <header class="relative z-10 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md shadow-md border-b border-gray-200 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- Page Content --}}
            <main class="relative z-10">
                {{ $slot }}
            </main>

            {{-- Footer Global (opcional, aparece solo en páginas que no tengan su propio footer) --}}
            <footer class="relative z-10 mt-auto py-6 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border-t border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row items-center justify-between text-center md:text-left">
                        <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>{{ config('app.name', 'DRG - Iris') }}</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-500">
                            © {{ date('Y') }} Sistema de Gestión Industrial. Todos los derechos reservados.
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        {{-- Scripts adicionales de las páginas --}}
        @stack('scripts')
    </body>
</html>