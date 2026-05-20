{{-- 
    layouts/partials/_sidebar-nav.blade.php
    Recibe $mode = 'desktop' | 'mobile'
    En desktop: los labels se ocultan cuando el sidebar está contraído
    En mobile:  siempre se muestran

    Visibilidad de secciones:
    - Todas las secciones: visibles para todos los roles autenticados
    - Sección "Administración" (Usuarios): solo Super Admin, Director, Gerente
--}}

@php
    $canManageUsers = auth()->check() && auth()->user()->hasAnyRole(['Super Admin', 'Director', 'Gerente']);

    $sections = [
        [
            'title' => null,
            'links' => [
                ['route' => 'dashboard', 'routeIs' => 'dashboard', 'icon' => 'fa-chart-pie', 'color' => 'text-indigo-400', 'label' => 'Dashboard'],
            ]
        ],
        [
            'title' => 'Almacén',
            'links' => [
                ['route' => 'containers.index', 'routeIs' => 'containers.*', 'icon' => 'fa-ship',           'color' => 'text-teal-400',    'label' => 'Contenedores'],
                ['route' => 'pallets.index',    'routeIs' => 'pallets.*',    'icon' => 'fa-box',            'color' => 'text-blue-400',    'label' => 'Tarimas'],
                ['route' => 'inventory.index',  'routeIs' => 'inventory.*',  'icon' => 'fa-boxes',          'color' => 'text-cyan-400',    'label' => 'Inventario'],
                ['route' => 'warehouse.locations','routeIs' => 'warehouse.*','icon' => 'fa-warehouse',      'color' => 'text-emerald-400', 'label' => 'Localidades'],
                ['route' => 'maquila.index',    'routeIs' => 'customs.*',    'icon' => 'fa-passport',       'color' => 'text-red-400',     'label' => 'Maquila'],
                ['route' => 'picking.index',    'routeIs' => 'picking.*',    'icon' => 'fa-clipboard-list', 'color' => 'text-orange-400',  'label' => 'Surtido'],
                ['route' => 'dispatch.index',   'routeIs' => 'dispatch.*',   'icon' => 'fa-truck',          'color' => 'text-green-400',   'label' => 'Despachos'],
            ]
        ],
        [
            'title' => 'Reportes',
            'links' => [
                ['route' => 'reports.storage-time', 'routeIs' => 'reports.*', 'icon' => 'fa-chart-line', 'color' => 'text-pink-400', 'label' => 'Lead Time (Almacenaje)'],
            ]
        ],
    ];

    // Sección Administración: solo para Super Admin, Director y Gerente
    if ($canManageUsers) {
        $sections[] = [
            'title' => 'Administración',
            'links' => [
                ['route' => 'users.index', 'routeIs' => 'users.*', 'icon' => 'fa-users', 'color' => 'text-violet-400', 'label' => 'Usuarios'],
            ]
        ];
    }

    $isDesktop = ($mode ?? 'desktop') === 'desktop';
@endphp

@foreach($sections as $section)
    <div>
        {{-- Título de sección --}}
        @if($section['title'])
            @if($isDesktop)
                {{-- Desktop expandido: título texto --}}
                <p x-show="expanded" x-transition.opacity.duration.200ms class="px-4 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                    {{ $section['title'] }}
                </p>
                {{-- Desktop contraído: separador --}}
                <div x-show="!expanded" class="border-t border-gray-700/50 mx-2 mb-2"></div>
            @else
                {{-- Mobile: siempre título --}}
                <p class="px-4 text-xs font-semibold tracking-wider text-gray-500 uppercase">{{ $section['title'] }}</p>
            @endif
        @endif

        <div class="{{ $section['title'] ? 'mt-2' : '' }} space-y-1">
            @foreach($section['links'] as $link)
                @php
                    $isActive = request()->routeIs($link['routeIs']);
                    $activeClass = $isActive ? 'text-white bg-gray-800' : 'text-gray-300 hover:text-white hover:bg-gray-800';
                @endphp

                <a href="{{ route($link['route']) }}"
                   @if(!$isDesktop) @click="mobileOpen = false" @endif
                   class="flex items-center rounded-lg transition-colors {{ $activeClass }}"
                   @if($isDesktop)
                       :class="expanded ? 'px-4 py-2.5' : 'px-0 py-2.5 justify-center'"
                   @else
                       class="px-4 py-2.5"
                   @endif
                   title="{{ $link['label'] }}">

                    <i class="fas {{ $link['icon'] }} {{ $link['color'] }}" 
                       @if($isDesktop) :class="expanded ? 'w-6 text-center' : 'text-lg'" @else class="w-6 text-center" @endif></i>

                    @if($isDesktop)
                        <span x-show="expanded" x-transition.opacity.duration.200ms class="mx-2 font-medium whitespace-nowrap">{{ $link['label'] }}</span>
                    @else
                        <span class="mx-2 font-medium">{{ $link['label'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endforeach