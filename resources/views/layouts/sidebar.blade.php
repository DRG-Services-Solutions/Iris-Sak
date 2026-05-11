{{-- 
    Sidebar Colapsable
    ─────────────────────────────────────────
    Desktop: siempre visible, contraído (68px) por defecto.
             Se expande (288px) al hacer hover o al fijar con pin.
    Mobile:  oculto por defecto, se abre como drawer con hamburguesa.
    
    El estado (pinned, hovered, expanded, mobileOpen) vive en el 
    x-data de <body> en app.blade.php para que el contenido principal
    pueda ajustar su margin-left.
--}}

{{-- ══════════════════════════════════════════ --}}
{{--  DESKTOP SIDEBAR (lg+)                    --}}
{{-- ══════════════════════════════════════════ --}}
<aside @mouseenter="sidebarEnter()" 
       @mouseleave="sidebarLeave()"
       class="hidden lg:flex fixed left-0 top-0 flex-col h-screen py-6 overflow-y-auto overflow-x-hidden bg-slate-900 shadow-2xl z-40 transition-all duration-300 ease-in-out"
       :class="expanded ? 'w-72 px-4' : 'w-[68px] px-2'">
    
    {{-- Header: logo + pin --}}
    <div class="flex items-center mb-6 px-2" :class="expanded ? 'justify-between' : 'justify-center'">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 min-w-0">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-2 rounded-lg flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span x-show="expanded" x-transition.opacity.duration.200ms class="text-2xl font-bold text-white whitespace-nowrap">Iris DRG</span>
        </a>
        <button x-show="expanded" x-transition.opacity 
                @click="togglePin()" 
                class="text-gray-400 hover:text-white transition p-1.5 rounded-lg hover:bg-gray-800 flex-shrink-0"
                :title="pinned ? 'Contraer sidebar' : 'Fijar sidebar abierto'">
            <i class="fas fa-thumbtack transition-transform duration-200" :class="pinned ? 'text-indigo-400 rotate-0' : 'rotate-45'"></i>
        </button>
    </div>

    <div class="flex flex-col justify-between flex-1">
        <nav class="space-y-6">
            @include('layouts.partials._sidebar-nav', ['mode' => 'desktop'])
        </nav>
        @include('layouts.partials._sidebar-user', ['mode' => 'desktop'])
    </div>
</aside>

{{-- ══════════════════════════════════════════ --}}
{{--  MOBILE SIDEBAR (drawer, < lg)            --}}
{{-- ══════════════════════════════════════════ --}}
<aside x-show="mobileOpen"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-250"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="lg:hidden fixed left-0 top-0 flex flex-col w-72 h-screen px-4 py-6 overflow-y-auto bg-slate-900 shadow-2xl z-40"
       x-cloak>
    
    {{-- Header: logo + cerrar --}}
    <div class="flex items-center justify-between mb-6 px-2">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-2 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span class="text-2xl font-bold text-white">Iris DRG</span>
        </a>
        <button @click="mobileOpen = false" class="text-gray-400 hover:text-white transition p-1.5 rounded-lg hover:bg-gray-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="flex flex-col justify-between flex-1">
        <nav class="space-y-6">
            @include('layouts.partials._sidebar-nav', ['mode' => 'mobile'])
        </nav>
        @include('layouts.partials._sidebar-user', ['mode' => 'mobile'])
    </div>
</aside>