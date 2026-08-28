@php
    $navGroups = [
        [
            'label' => __('admin/nav.groups.main'),
            'items' => [
                ['type' => 'link', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'fa-chart-pie', 'label' => __('admin/nav.dashboard')],
            ],
        ],
        [
            'label' => __('admin/nav.groups.catalog'),
            'items' => [
                ['type' => 'dropdown', 'active' => 'admin.products.*', 'icon' => 'fa-boxes-stacked', 'label' => __('admin/nav.products'), 'base' => 'admin.products'],
                ['type' => 'dropdown', 'active' => 'admin.categories.*', 'icon' => 'fa-layer-group', 'label' => __('admin/nav.categories'), 'base' => 'admin.categories'],
                ['type' => 'dropdown', 'active' => 'admin.branches.*', 'icon' => 'fa-store', 'label' => __('admin/nav.branches'), 'base' => 'admin.branches'],
            ],
        ],
        [
            'label' => __('admin/nav.groups.sales'),
            'items' => [
                ['type' => 'link', 'route' => 'admin.orders.index', 'active' => 'admin.orders.*', 'icon' => 'fa-cart-shopping', 'label' => __('admin/nav.orders')],
            ],
        ],
        [
            'label' => __('admin/nav.groups.system'),
            'items' => [
                ['type' => 'dropdown', 'active' => 'admin.users.*', 'icon' => 'fa-users', 'label' => __('admin/nav.users'), 'base' => 'admin.users'],
            ],
        ],
        [
            'label' => __('admin/nav.groups.address'),
            'items' => [
                ['type' => 'link', 'route' => 'admin.provinces.index', 'active' => 'admin.provinces.*', 'icon' => 'fa-map', 'label' => __('admin/nav.provinces')],
                ['type' => 'link', 'route' => 'admin.wards.index', 'active' => 'admin.wards.*', 'icon' => 'fa-map-location-dot', 'label' => __('admin/nav.wards')],
            ],
        ],
    ];

    $childLinks = [
        ['suffix' => 'index', 'icon' => 'fa-list', 'label' => __('admin/nav.index')],
        ['suffix' => 'create', 'icon' => 'fa-plus', 'label' => __('admin/nav.create')],
        ['suffix' => 'trash', 'icon' => 'fa-trash', 'label' => __('admin/nav.trash')],
    ];
@endphp

<aside id="sidebar">
    <div class="flex items-center justify-between px-5 py-5 border-b border-white/10">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center text-white font-bold">A</span>
            <span>
                <span class="block text-base font-bold text-white tracking-wide">{{ __('admin/nav.brand') }}</span>
                <span class="block text-[10px] font-semibold uppercase tracking-widest text-blue-400">
                    {{ __('admin/nav.brand_subtitle') }}
                </span>
            </span>
        </a>

        <button type="button" id="sidebarToggleMobile" class="lg:hidden text-slate-400 hover:text-white transition-colors">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <nav class="px-3 py-4 pb-10">
        @foreach ($navGroups as $group)
            <p class="px-4 pt-4 pb-2 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">
                {{ $group['label'] }}
            </p>

            <ul class="space-y-1">
                @foreach ($group['items'] as $item)
                    @php $isActive = request()->routeIs($item['active']); @endphp

                    @if ($item['type'] === 'link')
                        <li>
                            <a href="{{ route($item['route']) }}"
                                class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium {{ $isActive ? 'is-active' : '' }}">
                                <i class="fa-solid {{ $item['icon'] }} w-4 text-center"></i>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @else
                        <li class="sidebar-dropdown {{ $isActive ? 'active' : '' }}">
                            <a href="javascript:void(0)"
                                class="dropdown-toggle flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-medium {{ $isActive ? 'is-active' : '' }}">
                                <span class="flex items-center gap-3">
                                    <i class="fa-solid {{ $item['icon'] }} w-4 text-center"></i>
                                    {{ $item['label'] }}
                                </span>
                                <i class="fa-solid fa-chevron-right text-[10px] transition-transform duration-300 arrow-icon"></i>
                            </a>

                            <ul class="submenu hidden mt-1 ml-6 pl-3 border-l border-white/10 space-y-1">
                                @foreach ($childLinks as $child)
                                    @php $childRoute = $item['base'] . '.' . $child['suffix']; @endphp
                                    <li>
                                        <a href="{{ route($childRoute) }}"
                                            class="flex items-center gap-2 py-2 px-3 rounded-lg text-sm transition-colors {{ request()->routeIs($childRoute) ? 'text-blue-400 font-semibold' : 'text-slate-500 hover:text-white' }}">
                                            <i class="fa-solid {{ $child['icon'] }} w-3.5 text-center text-xs"></i>
                                            {{ $child['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endforeach
    </nav>
</aside>

<div id="overlay"></div>
