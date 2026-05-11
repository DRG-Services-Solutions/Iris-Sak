{{-- 
    layouts/partials/_sidebar-user.blade.php
    Recibe $mode = 'desktop' | 'mobile'
--}}

@php $isDesktop = ($mode ?? 'desktop') === 'desktop'; @endphp

<div class="mt-6 pt-4 border-t border-gray-700/50 space-y-2">
    
    {{-- Tarjeta de usuario --}}
    <div class="flex items-center bg-gray-800/50 rounded-xl border border-gray-700/50"
         @if($isDesktop) :class="expanded ? 'p-3' : 'p-2 justify-center'" @else class="p-3" @endif>
        <div class="flex-shrink-0">
            <div class="flex items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold border-2 border-slate-900 shadow-inner"
                 @if($isDesktop) :class="expanded ? 'w-10 h-10 text-lg' : 'w-8 h-8 text-sm'" @else class="w-10 h-10 text-lg" @endif>
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
        </div>

        @if($isDesktop)
            <div x-show="expanded" x-transition.opacity.duration.200ms class="ml-3 overflow-hidden min-w-0">
                <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs font-medium text-indigo-400 truncate">{{ Auth::user()->getRoleNames()->first() ?? 'Sin Rol' }}</p>
            </div>
        @else
            <div class="ml-3 overflow-hidden">
                <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs font-medium text-indigo-400 truncate">{{ Auth::user()->getRoleNames()->first() ?? 'Sin Rol' }}</p>
            </div>
        @endif
    </div>

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" 
                class="flex items-center w-full text-sm font-medium text-red-400 bg-red-400/10 transition-colors rounded-xl hover:text-white hover:bg-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none justify-center"
                @if($isDesktop) :class="expanded ? 'px-4 py-2.5' : 'px-0 py-2.5'" @else class="px-4 py-2.5" @endif
                title="Cerrar Sesión">
            <i class="fas fa-sign-out-alt" @if($isDesktop) :class="expanded ? 'mr-2' : ''" @else class="mr-2" @endif></i>
            @if($isDesktop)
                <span x-show="expanded" x-transition.opacity.duration.200ms>Cerrar Sesión</span>
            @else
                <span>Cerrar Sesión</span>
            @endif
        </button>
    </form>
</div>