<nav x-data="{ open: false }" class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="bg-gradient-to-br from-slate-700 to-slate-900 p-2.5 rounded-xl shadow-lg group-hover:shadow-xl transform group-hover:-translate-y-0.5 transition-all duration-200">
                            <x-application-logo class="w-8 h-8 text-white" />
                        </div>
                        <div class="hidden lg:block">
                            <div class="text-lg font-bold text-gray-900 dark:text-white">DRG - Iris</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 -mt-0.5">Gestión Industrial</div>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex items-center">

                    {{-- Dashboard --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- Catálogo de Productos (solo usuarios con permiso manage-products) --}}
                    @can('manage-products')
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            {{ __('Catálogo') }}
                        </x-nav-link>
                    @endcan

                    {{-- Control de Inventario --}}
                    <x-nav-link :href="route('inventory.index')" :active="request()->routeIs('inventory.*')"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        {{ __('Inventario') }}
                    </x-nav-link>

                    {{-- Movimientos --}}
                    <x-nav-link :href="route('movements.index')" :active="request()->routeIs('movements.*')"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ __('Movimientos') }}
                    </x-nav-link>

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-gray-200 dark:border-gray-700 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 shadow-sm hover:shadow-md">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 text-white font-bold text-xs">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <div class="hidden lg:block text-left">
                                    <div class="font-semibold text-sm">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                                </div>
                            </div>
                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 text-white font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-sm text-gray-900 dark:text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="py-1">
                            <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('Mi Perfil') }}
                            </x-dropdown-link>
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="flex items-center text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    {{ __('Cerrar Sesión') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button
                    @click="open = !open"
                    type="button"
                    class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-gray-200 dark:border-gray-700">
        <div class="pt-2 pb-3 space-y-1 bg-gray-50 dark:bg-gray-900">

            {{-- Dashboard --}}
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                                   class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            {{-- Catálogo de Productos (solo usuarios con permiso manage-products) --}}
            @can('manage-products')
                <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')"
                                       class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    {{ __('Catálogo') }}
                </x-responsive-nav-link>
            @endcan

            {{-- Control de Inventario --}}
            <x-responsive-nav-link :href="route('inventory.index')" :active="request()->routeIs('inventory.*')"
                                   class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                {{ __('Inventario') }}
            </x-responsive-nav-link>

            {{-- Movimientos --}}
            <x-responsive-nav-link :href="route('movements.index')" :active="request()->routeIs('movements.*')"
                                   class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Movimientos') }}
            </x-responsive-nav-link>

        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            <div class="px-4 mb-3">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-slate-600 to-slate-800 text-white font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('Mi Perfil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="flex items-center text-red-600 dark:text-red-400">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>