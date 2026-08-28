@php
    $navGroups = [
        [
            'label' => __('admin/nav.groups.main'),
            'items' => [
                [
                    'type' => 'link',
                    'route' => 'admin.dashboard',
                    'active' => 'admin.dashboard',
                    'icon' => 'fa-chart-pie',
                    'label' => __('admin/nav.dashboard'),
                ],
            ],
        ],
        [
            'label' => __('admin/nav.groups.catalog'),
            'items' => [
                [
                    'type' => 'dropdown',
                    'active' => 'admin.products.*',
                    'icon' => 'fa-boxes',
                    'label' => __('admin/nav.products'),
                    'children' => [
                        ['route' => 'admin.products.index', 'icon' => 'fa-list', 'label' => __('admin/nav.index')],
                        ['route' => 'admin.products.create', 'icon' => 'fa-plus', 'label' => __('admin/nav.create')],
                        ['route' => 'admin.products.trash', 'icon' => 'fa-trash', 'label' => __('admin/nav.trash')],
                    ],
                ],
                [
                    'type' => 'dropdown',
                    'active' => 'admin.categories.*',
                    'icon' => 'fa-layer-group',
                    'label' => __('admin/nav.categories'),
                    'children' => [
                        ['route' => 'admin.categories.index', 'icon' => 'fa-list', 'label' => __('admin/nav.index')],
                        ['route' => 'admin.categories.create', 'icon' => 'fa-plus', 'label' => __('admin/nav.create')],
                        ['route' => 'admin.categories.trash', 'icon' => 'fa-trash', 'label' => __('admin/nav.trash')],
                    ],
                ],
                [
                    'type' => 'dropdown',
                    'active' => 'admin.branches.*',
                    'icon' => 'fa-store',
                    'label' => __('admin/nav.branches'),
                    'children' => [
                        ['route' => 'admin.branches.index', 'icon' => 'fa-list', 'label' => __('admin/nav.index')],
                        ['route' => 'admin.branches.create', 'icon' => 'fa-plus', 'label' => __('admin/nav.create')],
                        ['route' => 'admin.branches.trash', 'icon' => 'fa-trash', 'label' => __('admin/nav.trash')],
                    ],
                ],
            ],
        ],
        [
            'label' => __('admin/nav.groups.sales'),
            'items' => [
                ['type' => 'soon', 'icon' => 'fa-cart-shopping', 'label' => __('admin/nav.orders')],
            ],
        ],
        [
            'label' => __('admin/nav.groups.system'),
            'items' => [
                [
                    'type' => 'dropdown',
                    'active' => 'admin.users.*',
                    'icon' => 'fa-users',
                    'label' => __('admin/nav.users'),
                    'children' => [
                        ['route' => 'admin.users.index', 'icon' => 'fa-list', 'label' => __('admin/nav.index')],
                        ['route' => 'admin.users.create', 'icon' => 'fa-plus', 'label' => __('admin/nav.create')],
                        ['route' => 'admin.users.trash', 'icon' => 'fa-trash', 'label' => __('admin/nav.trash')],
                    ],
                ],
            ],
        ],
        [
            'label' => __('admin/nav.groups.address'),
            'items' => [
                [
                    'type' => 'link',
                    'route' => 'admin.provinces.index',
                    'active' => 'admin.provinces.*',
                    'icon' => 'fa-map',
                    'label' => __('admin/nav.provinces'),
                ],
                [
                    'type' => 'link',
                    'route' => 'admin.wards.index',
                    'active' => 'admin.wards.*',
                    'icon' => 'fa-map-location-dot',
                    'label' => __('admin/nav.wards'),
                ],
            ],
        ],
    ];
@endphp

<aside class="bg-[#1e1e2d] text-gray-300 w-64 min-h-screen fixed left-0 top-0 z-50 transition-all duration-300"
    id="sidebar">
    <div class="p-6 flex items-center justify-between border-b border-gray-700/50">
        <div>
            <h1 class="text-xl font-bold text-white tracking-wider">{{ __('admin/nav.brand') }}</h1>
            <p class="text-[10px] text-blue-400 font-semibold uppercase">{{ __('admin/nav.brand_subtitle') }}</p>
        </div>
        <button type="button" id="sidebarToggleMobile" class="lg:hidden text-gray-400 hover:text-white">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <nav class="mt-4 px-3 pb-6">
        @foreach ($navGroups as $group)
            <div class="mb-4">
                <p class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest">
                    {{ $group['label'] }}
                </p>

                <ul class="space-y-1">
                    @foreach ($group['items'] as $item)
                        @if ($item['type'] === 'link')
                            <li>
                                <a href="{{ route($item['route']) }}"
                                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs($item['active']) ? 'bg-blue-600 text-white shadow-lg' : 'hover:bg-gray-800' }}">
                                    <i class="fa-solid {{ $item['icon'] }} w-5"></i>
                                    <span class="font-medium text-sm">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @elseif ($item['type'] === 'dropdown')
                            <li class="sidebar-dropdown {{ request()->routeIs($item['active']) ? 'active' : '' }}">
                                <a href="javascript:void(0)"
                                    class="dropdown-toggle flex items-center justify-between px-4 py-3 rounded-lg transition-all {{ request()->routeIs($item['active']) ? 'bg-blue-600 text-white shadow-lg' : 'hover:bg-gray-800' }}">
                                    <span class="flex items-center gap-3">
                                        <i class="fa-solid {{ $item['icon'] }} w-5"></i>
                                        <span class="font-medium text-sm">{{ $item['label'] }}</span>
                                    </span>
                                    <i
                                        class="fa-solid fa-chevron-right text-[10px] transition-transform duration-300 arrow-icon"></i>
                                </a>

                                <ul class="submenu hidden mt-1 ml-4 border-l border-gray-700 space-y-1">
                                    @foreach ($item['children'] as $child)
                                        <li>
                                            <a href="{{ route($child['route']) }}"
                                                class="flex items-center gap-2 py-2 px-6 text-sm hover:text-white transition-colors {{ request()->routeIs($child['route']) ? 'text-blue-400 font-bold' : 'text-gray-500' }}">
                                                <i class="fa-solid {{ $child['icon'] }} w-4"></i>
                                                {{ $child['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li>
                                <span
                                    class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-gray-500 cursor-not-allowed">
                                    <span class="flex items-center gap-3">
                                        <i class="fa-solid {{ $item['icon'] }} w-5"></i>
                                        <span class="font-medium text-sm">{{ $item['label'] }}</span>
                                    </span>
                                    <span
                                        class="text-[9px] uppercase tracking-wider bg-gray-700/60 text-gray-400 px-1.5 py-0.5 rounded">
                                        {{ __('admin/nav.coming_soon') }}
                                    </span>
                                </span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>
</aside>

<div id="overlay"></div>
