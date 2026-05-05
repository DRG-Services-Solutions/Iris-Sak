{{-- Sidebar Overlay/Drawer --}}
<aside x-show="open"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-250"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="fixed left-0 top-0 flex flex-col w-72 h-screen px-4 py-6 overflow-y-auto bg-slate-900 shadow-2xl z-40"
       x-cloak>
    
    {{-- Header del sidebar: logo + cerrar --}}
    <div class="flex items-center justify-between mb-6 px-2">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-2 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span class="text-2xl font-bold text-white">Iris DRG</span>
        </a>
        <button @click="closeSidebar()" class="text-gray-400 hover:text-white transition p-1.5 rounded-lg hover:bg-gray-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="flex flex-col justify-between flex-1">
        <nav class="space-y-6">
            
            {{-- PRINCIPAL --}}
            <div class="space-y-1">
                <a @click="if(window.innerWidth < 1024) closeSidebar()" 
                   class="flex items-center px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}" 
                   href="{{ route('dashboard') }}">
                    <i class="fas fa-chart-pie w-6 text-center text-indigo-400"></i>
                    <span class="mx-2 font-medium">Dashboard</span>
                </a>
            </div>

            {{-- ALMACÉN --}}
            <div>
                <p class="px-4 text-xs font-semibold tracking-wider text-gray-500 uppercase">Almacén</p>
                <div class="mt-2 space-y-1">
                    <a @click="if(window.innerWidth < 1024) closeSidebar()"
                       class="flex items-center px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('containers.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}" 
                       href="{{ route('containers.index') }}">
                        <i class="fas fa-ship w-6 text-center text-teal-400"></i>
                        <span class="mx-2 font-medium">Contenedores</span>
                    </a>
                    <!-- Tarimas -->
                    <a @click="if(window.innerWidth < 1024) closeSidebar()"
                       class="flex items-center px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('pallets.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}" 
                       href="{{ route('pallets.index') }}">
                        <i class="fas fa-box w-6 text-center text-blue-400"></i>
                        <span class="mx-2 font-medium">Tarimas</span>
                    </a>
                    <a @click="if(window.innerWidth < 1024) closeSidebar()"
                       class="flex items-center px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('warehouse.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}" 
                       href="{{ route('warehouse.locations') }}">
                        <i class="fas fa-warehouse w-6 text-center text-emerald-400"></i>
                        <span class="mx-2 font-medium">Localidades</span>
                    </a>
                    {{-- Maquila --}}
                    <a @click="if(window.innerWidth < 1024) closeSidebar()"
                       class="flex items-center px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('customs.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}" 
                       href="#">
                        <i class="fas fa-passport w-6 text-center text-red-400"></i>
                        <span class="mx-2 font-medium">Maquila</span>
                    </a>
                    <a @click="if(window.innerWidth < 1024) closeSidebar()"
                       class="flex items-center px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('picking.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}" 
                       href="{{ route('picking.index') }}">
                        <i class="fas fa-clipboard-list w-6 text-center text-orange-400"></i>
                        <span class="mx-2 font-medium">Surtido</span>
                    </a>
                    <a @click="if(window.innerWidth < 1024) closeSidebar()"
                       class="flex items-center px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dispatch.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}" 
                       href="{{ route('dispatch.index') }}">
                        <i class="fas fa-truck w-6 text-center text-green-400"></i>
                        <span class="mx-2 font-medium">Despachos</span>
                    </a>
                </div>
            </div>

            

            {{-- ADMINISTRACIÓN --}}
            <div>
                <p class="px-4 text-xs font-semibold tracking-wider text-gray-500 uppercase">Administración</p>
                <div class="mt-2 space-y-1">
                    <a @click="if(window.innerWidth < 1024) closeSidebar()"
                       class="flex items-center px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}" 
                       href="{{ route('users.index') }}">
                        <i class="fas fa-users w-6 text-center text-violet-400"></i>
                        <span class="mx-2 font-medium">Usuarios</span>
                    </a>

                    <a @click="if(window.innerWidth < 1024) closeSidebar()"
                       class="flex items-center px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('roles.*') ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}" 
                       href="{{ route('roles.index') }}">
                        <i class="fas fa-user-shield w-6 text-center text-emerald-400"></i>
                        <span class="mx-2 font-medium">Roles y Permisos</span>
                    </a>
                </div>
            </div>

           

        </nav>
        
        {{-- USUARIO Y LOGOUT --}}
        <div class="mt-6 pt-4 border-t border-gray-700/50 space-y-2">
            
            <div class="flex items-center p-3 bg-gray-800/50 rounded-xl border border-gray-700/50">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-lg border-2 border-slate-900 shadow-inner">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
                <div class="ml-3 overflow-hidden">
                    <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs font-medium text-indigo-400 truncate">{{ Auth::user()->getRoleNames()->first() ?? 'Sin Rol' }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center justify-center w-full px-4 py-2.5 text-sm font-medium text-red-400 bg-red-400/10 transition-colors rounded-xl hover:text-white hover:bg-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </div>
</aside>
