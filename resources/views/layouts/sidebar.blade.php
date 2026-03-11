<aside class="flex flex-col w-64 h-screen px-4 py-8 overflow-y-auto bg-slate-900 border-r rtl:border-r-0 rtl:border-l dark:bg-gray-900 dark:border-gray-700">
    {{-- Logotipo / Marca --}}
    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-2">
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-2 rounded-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
        </div>
        <span class="text-2xl font-bold text-white">Iris DRG</span>
    </a>

    <div class="flex flex-col justify-between flex-1 mt-6">
        <nav class="space-y-6">
            
            {{-- SECCIÓN: PRINCIPAL --}}
            <div class="space-y-2">
                <a class="flex items-center px-4 py-2.5 text-gray-100 bg-gray-800 rounded-lg transition-colors hover:bg-gray-800" href="{{ route('dashboard') }}">
                    <i class="fas fa-chart-pie w-6 text-center text-indigo-400"></i>
                    <span class="mx-2 font-medium">Dashboard</span>
                </a>
            </div>

            {{-- SECCIÓN: OPERACIONES --}}
            @can('manage-products') {{-- Reemplaza con tus permisos reales si tienes otros --}}
            <div>
                <p class="px-4 text-xs font-semibold tracking-wider text-gray-400 uppercase">Operaciones</p>
                <div class="mt-2 space-y-1">
                    <a class="flex items-center px-4 py-2.5 text-gray-300 transition-colors rounded-lg hover:text-white hover:bg-gray-800" href="{{ route('products.index') }}">
                        <i class="fas fa-tools w-6 text-center text-amber-400"></i>
                        <span class="mx-2 font-medium">Catálogo</span>
                    </a>

                    
                    
                    <a class="flex items-center px-4 py-2.5 text-gray-300 transition-colors rounded-lg hover:text-white hover:bg-gray-800" href="{{ route('movements.index') }}">
                        <i class="fas fa-exchange-alt w-6 text-center text-orange-400"></i>
                        <span class="mx-2 font-medium">Movimientos</span>
                    </a>

                    <a class="flex items-center px-4 py-2.5 text-gray-300 transition-colors rounded-lg hover:text-white hover:bg-gray-800" href="{{ route('work_orders.index') }}">
                        <i class="fas fa-microchip w-6 text-center text-blue-400"></i>
                        <span class="mx-2 font-medium">Gestión RFID</span>
                    </a>

                    <a class="flex items-center px-4 py-2.5 text-gray-300 transition-colors rounded-lg hover:text-white hover:bg-gray-800" href="{{ route('inventory.index') }}">
                        <i class="fas fa-boxes w-6 text-center text-green-400"></i>
                        <span class="mx-2 font-medium">Existencias</span>
                    </a>

                    
                </div>
            </div>
            @endcan

            {{-- SECCIÓN: ADMINISTRACIÓN --}}
            <div>
                <p class="px-4 text-xs font-semibold tracking-wider text-gray-400 uppercase">Administración</p>
                <div class="mt-2 space-y-1">
                    <a class="flex items-center px-4 py-2.5 text-gray-300 transition-colors rounded-lg hover:text-white hover:bg-gray-800" href="{{ route('users.index') }}">
                        <i class="fas fa-users w-6 text-center text-violet-400"></i>
                        <span class="mx-2 font-medium">Usuarios</span>
                    </a>

                    <a class="flex items-center px-4 py-2.5 text-gray-300 transition-colors rounded-lg hover:text-white hover:bg-gray-800" href="{{ route('roles.index') }}">
                        <i class="fas fa-user-shield w-6 text-center text-emerald-400"></i>
                        <span class="mx-2 font-medium">Roles y Permisos</span>
                    </a>
                </div>
            </div>

            {{-- SECCIÓN: SUPER ADMIN --}}
            @role('Super Admin')
            <div>
                <p class="px-4 text-xs font-semibold tracking-wider text-indigo-400 uppercase">Global</p>
                <div class="mt-2 space-y-1">
                    <a class="flex items-center px-4 py-2.5 text-gray-300 transition-colors rounded-lg hover:text-white hover:bg-gray-800" href="{{ route('tenants.index') }}">
                        <i class="fas fa-building w-6 text-center text-indigo-400"></i>
                        <span class="mx-2 font-medium">Clientes</span>
                    </a>
                </div>
            </div>
            @endrole

        </nav>
        
        {{-- ZONA DE USUARIO Y SALIDA --}}
        <div class="mt-8 pt-4 border-t border-gray-200/10 dark:border-gray-700/50 space-y-2">
            
            {{-- Tarjeta de Usuario --}}
            <div class="flex items-center p-3 bg-gray-800/50 dark:bg-gray-800/80 rounded-xl border border-gray-700/50 backdrop-blur-sm transition-all hover:bg-gray-800">
                {{-- Avatar Dinámico (Primera letra del nombre) --}}
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-lg border-2 border-slate-900 shadow-inner">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
                {{-- Info del Usuario --}}
                <div class="ml-3 overflow-hidden">
                    <p class="text-sm font-bold text-white truncate" title="{{ Auth::user()->name }}">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-xs font-medium text-indigo-400 truncate" title="{{ Auth::user()->getRoleNames()->first() ?? 'Sin Rol' }}">
                        {{ Auth::user()->getRoleNames()->first() ?? 'Sin Rol' }}
                    </p>
                </div>
            </div>

            {{-- Botón de Cerrar Sesión --}}
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