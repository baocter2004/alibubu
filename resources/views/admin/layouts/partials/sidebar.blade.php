@php
    $navGroups = [
        [
            'label' => __('admin/nav.groups.main'),
            'items' => [
                ['type' => 'link', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'fa-gauge-high', 'label' => __('admin/nav.dashboard')],
            ],
        ],
        [
            'label' => __('admin/nav.groups.catalog'),
            'items' => [
                ['type' => 'dropdown', 'active' => 'admin.products.*', 'icon' => 'fa-mobile-screen-button', 'label' => __('admin/nav.products'), 'base' => 'admin.products'],
                ['type' => 'dropdown', 'active' => 'admin.categories.*', 'icon' => 'fa-sitemap', 'label' => __('admin/nav.categories'), 'base' => 'admin.categories'],
                ['type' => 'dropdown', 'active' => 'admin.branches.*', 'icon' => 'fa-award', 'label' => __('admin/nav.branches'), 'base' => 'admin.branches'],
                ['type' => 'dropdown', 'active' => 'admin.attributes.*', 'icon' => 'fa-sliders', 'label' => __('admin/nav.attributes'), 'base' => 'admin.attributes'],
                ['type' => 'dropdown', 'active' => 'admin.tags.*', 'icon' => 'fa-tags', 'label' => __('admin/nav.tags'), 'base' => 'admin.tags'],
            ],
        ],
        [
            'label' => __('admin/nav.groups.sales'),
            'items' => [
                ['type' => 'link', 'route' => 'admin.orders.index', 'active' => 'admin.orders.*', 'icon' => 'fa-receipt', 'label' => __('admin/nav.orders')],
                ['type' => 'dropdown', 'active' => 'admin.coupons.*', 'icon' => 'fa-ticket', 'label' => __('admin/nav.coupons'), 'base' => 'admin.coupons'],
            ],
        ],
        [
            'label' => __('admin/nav.groups.system'),
            'items' => [
                ['type' => 'dropdown', 'active' => 'admin.users.*', 'icon' => 'fa-user-group', 'label' => __('admin/nav.users'), 'base' => 'admin.users'],
            ],
        ],
        [
            'label' => __('admin/nav.groups.address'),
            'items' => [
                ['type' => 'link', 'route' => 'admin.provinces.index', 'active' => 'admin.provinces.*', 'icon' => 'fa-map-location-dot', 'label' => __('admin/nav.provinces')],
                ['type' => 'link', 'route' => 'admin.wards.index', 'active' => 'admin.wards.*', 'icon' => 'fa-location-dot', 'label' => __('admin/nav.wards')],
            ],
        ],
    ];

    $childLinks = [
        ['suffix' => 'index', 'icon' => 'fa-list-ul', 'label' => __('admin/nav.index')],
        ['suffix' => 'create', 'icon' => 'fa-circle-plus', 'label' => __('admin/nav.create')],
        ['suffix' => 'trash', 'icon' => 'fa-trash-can', 'label' => __('admin/nav.trash')],
    ];
@endphp

<aside id="sidebar">
    <div class="flex items-center justify-between gap-2 px-4 py-4 border-b border-white/10">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand flex items-center gap-3 min-w-0">
            <span class="w-10 h-10 shrink-0 rounded-xl bg-blue-500 flex items-center justify-center text-white font-bold">
                A
            </span>
            <span class="brand-text min-w-0">
                <span class="block text-base font-bold text-white truncate">{{ __('admin/nav.brand') }}</span>
                <span class="block text-[10px] font-semibold uppercase tracking-widest text-blue-400 truncate">
                    {{ __('admin/nav.brand_subtitle') }}
                </span>
            </span>
        </a>

        <button type="button" id="sidebarClose"
            class="brand-text lg:hidden w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <nav class="sidebar-scroll py-3">
        @foreach ($navGroups as $group)
            <span class="group-divider"></span>
            <p class="group-label px-5 pt-3 pb-2 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">
                {{ $group['label'] }}
            </p>

            @foreach ($group['items'] as $item)
                @php $isActive = request()->routeIs($item['active']); @endphp

                @if ($item['type'] === 'link')
                    <a href="{{ route($item['route']) }}" data-tooltip="{{ $item['label'] }}"
                        class="nav-item {{ $isActive ? 'is-active' : '' }}">
                        <i class="fa-solid {{ $item['icon'] }} nav-icon"></i>
                        <span class="sidebar-label">{{ $item['label'] }}</span>
                    </a>
                @else
                    <div class="sidebar-dropdown {{ $isActive ? 'active' : '' }}">
                        <a href="{{ route($item['base'] . '.index') }}" data-tooltip="{{ $item['label'] }}"
                            class="dropdown-toggle nav-item {{ $isActive ? 'is-active' : '' }}">
                            <i class="fa-solid {{ $item['icon'] }} nav-icon"></i>
                            <span class="sidebar-label flex-1">{{ $item['label'] }}</span>
                            <i class="fa-solid fa-chevron-right text-[10px] arrow-icon"></i>
                        </a>

                        <div class="submenu hidden mx-3 mt-1 mb-1 pl-4 border-l border-white/10 space-y-0.5">
                            @foreach ($childLinks as $child)
                                @php $childRoute = $item['base'] . '.' . $child['suffix']; @endphp
                                <a href="{{ route($childRoute) }}"
                                    class="submenu-link {{ request()->routeIs($childRoute) ? 'is-active' : '' }}">
                                    <i class="fa-solid {{ $child['icon'] }} w-3.5 text-center text-xs"></i>
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @endforeach
    </nav>

    <div class="px-3 py-3 border-t border-white/10">
        <a href="{{ route('index') }}" target="_blank" rel="noopener" data-tooltip="{{ __('admin/nav.view_site') }}"
            class="nav-item">
            <i class="fa-solid fa-arrow-up-right-from-square nav-icon"></i>
            <span class="sidebar-label">{{ __('admin/nav.view_site') }}</span>
        </a>
    </div>
</aside>

<div id="overlay"></div>
