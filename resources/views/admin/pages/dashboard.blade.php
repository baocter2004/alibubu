@extends('admin.layouts.app')

@section('title', __('admin/dashboard.title'))

@section('content')
    @php
        $admin = Auth::guard('admin')->user();
        $cards = [
            ['key' => 'revenue', 'icon' => 'fa-sack-dollar', 'tone' => 'rose', 'value' => format_price($stats['revenue']), 'route' => 'admin.orders.index', 'wide' => true],
            ['key' => 'orders', 'icon' => 'fa-receipt', 'tone' => 'sky', 'value' => number_format($stats['orders']), 'route' => 'admin.orders.index'],
            ['key' => 'products', 'icon' => 'fa-mobile-screen-button', 'tone' => 'purple', 'value' => number_format($stats['products']), 'route' => 'admin.products.index'],
            ['key' => 'users', 'icon' => 'fa-user-group', 'tone' => 'blue', 'value' => number_format($stats['users']), 'route' => 'admin.users.index'],
            ['key' => 'categories', 'icon' => 'fa-sitemap', 'tone' => 'amber', 'value' => number_format($stats['categories']), 'route' => 'admin.categories.index'],
            ['key' => 'branches', 'icon' => 'fa-award', 'tone' => 'emerald', 'value' => number_format($stats['branches']), 'route' => 'admin.branches.index'],
        ];
        $tones = [
            'blue' => ['bg-primary-soft', 'text-primary'],
            'purple' => ['bg-primary-soft', 'text-primary'],
            'amber' => ['bg-primary-soft', 'text-primary'],
            'emerald' => ['bg-primary-soft', 'text-primary'],
            'sky' => ['bg-primary-soft', 'text-primary'],
            'rose' => ['bg-accent-soft', 'text-accent'],
        ];
        $periodCards = [
            ['label' => __('admin/dashboard.period.revenue'), 'value' => format_price($period['revenue']), 'icon' => 'fa-chart-line'],
            ['label' => __('admin/dashboard.period.orders'), 'value' => number_format($period['orders']), 'icon' => 'fa-bag-shopping'],
            ['label' => __('admin/dashboard.period.paid_orders'), 'value' => number_format($period['paid_orders']), 'icon' => 'fa-circle-check'],
            ['label' => __('admin/dashboard.period.average_order'), 'value' => format_price($period['average_order']), 'icon' => 'fa-calculator'],
        ];
        $dashboardChartData = [
            'labels' => $chart['labels'],
            'revenue' => $chart['revenue'],
            'orders' => $chart['orders'],
            'status' => [
                'labels' => array_values(\App\Const\OrderConst::statuses()),
                'data' => array_values($statusCounts),
            ],
            'payment' => [
                'labels' => array_values(\App\Const\PaymentConst::methods()),
                'data' => array_values($paymentCounts),
            ],
            'topProducts' => [
                'labels' => $topProducts->pluck('name')->values(),
                'data' => $topProducts->pluck('revenue')->map(fn ($value) => (float) $value)->values(),
                'quantities' => $topProducts->pluck('quantity')->map(fn ($value) => (int) $value)->values(),
            ],
            'inventory' => [
                'labels' => [
                    __('admin/dashboard.charts.in_stock'),
                    __('admin/dashboard.charts.low_stock'),
                    __('admin/dashboard.charts.out_of_stock'),
                ],
                'data' => [
                    (int) $inventory['in_stock'],
                    (int) $inventory['low_stock'],
                    (int) $inventory['out_of_stock'],
                ],
            ],
            'config' => $chartConfig,
        ];
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin/dashboard.welcome', ['name' => $admin?->name]) }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('admin/dashboard.subtitle') }}</p>
        </div>

        <a href="{{ route('admin.products.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i>
            {{ __('admin/product.title.create') }}
        </a>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-4">
        <div>
            <p class="text-sm font-semibold text-gray-900">{{ __('admin/dashboard.period.title') }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $period['from'] }} – {{ $period['to'] }}</p>
        </div>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-end gap-2">
            <div>
                <label for="dashboard-range" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin/dashboard.period.range') }}</label>
                <select id="dashboard-range" name="range" class="min-w-36 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/30">
                    @foreach ([7, 30, 90, 365] as $days)
                        <option value="{{ $days }}" @selected($selectedRange === $days)>{{ __('admin/dashboard.period.days', ['count' => $days]) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="low-stock-threshold" class="block text-xs font-medium text-gray-500 mb-1">{{ __('admin/dashboard.period.low_stock_threshold') }}</label>
                <input id="low-stock-threshold" name="low_stock_threshold" type="number" min="1" max="100" value="{{ $lowStockThreshold }}"
                    class="w-24 px-3 py-2 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/30 focus:border-accent">
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors">
                <i class="fa-solid fa-filter"></i>
                {{ __('admin/dashboard.period.apply') }}
            </button>
        </form>
    </div>

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-6">
        @foreach ($periodCards as $card)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-center gap-2 text-gray-500 mb-3">
                    <span class="w-8 h-8 rounded-lg bg-primary-soft text-primary flex items-center justify-center">
                        <i class="fa-solid {{ $card['icon'] }} text-xs"></i>
                    </span>
                    <span class="text-xs sm:text-sm font-medium truncate">{{ $card['label'] }}</span>
                </div>
                <p class="text-lg sm:text-2xl font-bold text-gray-900 truncate">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div id="admin-dashboard" data-admin-dashboard
        data-chart-data="{{ json_encode($dashboardChartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
        data-locale="{{ app()->getLocale() === 'vi' ? 'vi-VN' : 'en-US' }}" data-currency="VND"
        data-revenue-label="{{ __('admin/dashboard.charts.revenue') }}"
        data-orders-label="{{ __('admin/dashboard.charts.orders') }}"
        data-quantity-label="{{ __('admin/dashboard.charts.quantity') }}"
        data-status-total-label="{{ __('admin/dashboard.charts.total') }}"
        data-inventory-total-label="{{ __('admin/dashboard.charts.total') }}"
        data-payment-total-label="{{ __('admin/dashboard.charts.total') }}">
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6 items-stretch">
        <section class="flex min-w-0 flex-col bg-white rounded-xl border border-gray-100 shadow-sm p-5" aria-labelledby="dashboard-revenue-title">
            <div class="mb-4">
                <h2 id="dashboard-revenue-title" class="font-semibold text-gray-900">{{ __('admin/dashboard.charts.revenue') }}</h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('admin/dashboard.charts.revenue_hint') }}</p>
            </div>
            <div class="relative h-64 sm:h-72"><canvas id="revenue-chart" role="img" aria-label="{{ __('admin/dashboard.charts.revenue_accessible') }}"></canvas></div>
            <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1 text-xs text-gray-500 mt-3 pt-3 border-t border-gray-100">
                <span>{{ __('admin/dashboard.charts.axis_min') }}: {{ number_format($chartConfig['revenue']['min']) }}</span>
                <span>{{ __('admin/dashboard.charts.axis_step') }}: {{ number_format($chartConfig['revenue']['step']) }}</span>
                <span>{{ __('admin/dashboard.charts.axis_max') }}: {{ number_format($chartConfig['revenue']['max']) }}</span>
            </div>
        </section>

        <section class="flex min-w-0 flex-col bg-white rounded-xl border border-gray-100 shadow-sm p-5" aria-labelledby="dashboard-orders-title">
            <div class="mb-4">
                <h2 id="dashboard-orders-title" class="font-semibold text-gray-900">{{ __('admin/dashboard.charts.orders') }}</h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('admin/dashboard.charts.orders_hint') }}</p>
            </div>
            <div class="relative h-64 sm:h-72"><canvas id="orders-chart" role="img" aria-label="{{ __('admin/dashboard.charts.orders_accessible') }}"></canvas></div>
            <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1 text-xs text-gray-500 mt-3 pt-3 border-t border-gray-100">
                <span>{{ __('admin/dashboard.charts.axis_min') }}: {{ number_format($chartConfig['orders']['min']) }}</span>
                <span>{{ __('admin/dashboard.charts.axis_step') }}: {{ number_format($chartConfig['orders']['step']) }}</span>
                <span>{{ __('admin/dashboard.charts.axis_max') }}: {{ number_format($chartConfig['orders']['max']) }}</span>
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6 items-stretch">
        <section class="flex min-w-0 flex-col xl:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-5" aria-labelledby="dashboard-mixed-title">
            <div class="mb-4">
                <h2 id="dashboard-mixed-title" class="font-semibold text-gray-900">{{ __('admin/dashboard.charts.mixed') }}</h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('admin/dashboard.charts.mixed_hint') }}</p>
            </div>
            <div class="relative h-64 sm:h-72"><canvas id="mixed-performance-chart" role="img" aria-label="{{ __('admin/dashboard.charts.mixed_accessible') }}"></canvas></div>
            <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1 text-xs text-gray-500 mt-3 pt-3 border-t border-gray-100">
                <span>{{ __('admin/dashboard.charts.revenue') }} · {{ __('admin/dashboard.charts.axis_min') }}: {{ number_format($chartConfig['revenue']['min']) }}</span>
                <span>{{ __('admin/dashboard.charts.revenue') }} · {{ __('admin/dashboard.charts.axis_step') }}: {{ number_format($chartConfig['revenue']['step']) }}</span>
                <span>{{ __('admin/dashboard.charts.revenue') }} · {{ __('admin/dashboard.charts.axis_max') }}: {{ number_format($chartConfig['revenue']['max']) }}</span>
                <span>{{ __('admin/dashboard.charts.orders') }} · {{ __('admin/dashboard.charts.axis_max') }}: {{ number_format($chartConfig['orders']['max']) }}</span>
            </div>
        </section>

        <section class="flex min-w-0 flex-col bg-white rounded-xl border border-gray-100 shadow-sm p-5" aria-labelledby="dashboard-payment-title">
            <div class="mb-5">
                <h2 id="dashboard-payment-title" class="font-semibold text-gray-900">{{ __('admin/dashboard.charts.payment') }}</h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('admin/dashboard.charts.payment_hint') }}</p>
            </div>
            <div class="relative h-64 sm:h-72"><canvas id="payment-chart" role="img" aria-label="{{ __('admin/dashboard.charts.payment_accessible') }}"></canvas></div>
            <div class="mt-3 flex flex-wrap items-center justify-between gap-x-3 gap-y-1 border-t border-gray-100 pt-3 text-xs text-gray-500">
                <span>{{ __('admin/dashboard.charts.axis_min') }}: {{ number_format($chartConfig['payment']['min']) }}</span>
                <span>{{ __('admin/dashboard.charts.axis_max') }}: {{ number_format($chartConfig['payment']['max']) }}</span>
                <span>{{ __('admin/dashboard.charts.total') }}: {{ number_format(array_sum($paymentCounts)) }}</span>
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6 items-stretch">
        <section class="flex min-w-0 flex-col bg-white rounded-xl border border-gray-100 shadow-sm p-5" aria-labelledby="dashboard-status-title">
            <div class="mb-5">
                <h2 id="dashboard-status-title" class="font-semibold text-gray-900">{{ __('admin/dashboard.charts.status') }}</h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('admin/dashboard.charts.status_hint') }}</p>
            </div>
            <div class="relative h-64 sm:h-72"><canvas id="status-chart" role="img" aria-label="{{ __('admin/dashboard.charts.status_accessible') }}"></canvas></div>
            <div class="mt-3 flex flex-wrap items-center justify-between gap-x-3 gap-y-1 border-t border-gray-100 pt-3 text-xs text-gray-500">
                <span>{{ __('admin/dashboard.charts.axis_min') }}: {{ number_format($chartConfig['status']['min']) }}</span>
                <span>{{ __('admin/dashboard.charts.axis_max') }}: {{ number_format($chartConfig['status']['max']) }}</span>
                <span>{{ __('admin/dashboard.charts.total') }}: {{ number_format(array_sum($statusCounts)) }}</span>
            </div>
        </section>

        <section class="flex min-w-0 flex-col bg-white rounded-xl border border-gray-100 shadow-sm p-5" aria-labelledby="dashboard-top-products-title">
            <div class="mb-5">
                <h2 id="dashboard-top-products-title" class="font-semibold text-gray-900">{{ __('admin/dashboard.charts.top_products') }}</h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('admin/dashboard.charts.top_products_hint') }}</p>
            </div>
            @if ($topProducts->isEmpty())
                <p class="py-10 text-center text-sm text-gray-500">{{ __('admin/dashboard.charts.no_data') }}</p>
            @else
                <div class="relative h-64 sm:h-72"><canvas id="top-products-chart" role="img" aria-label="{{ __('admin/dashboard.charts.top_products_accessible') }}"></canvas></div>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-x-3 gap-y-1 border-t border-gray-100 pt-3 text-xs text-gray-500">
                    <span>{{ __('admin/dashboard.charts.axis_min') }}: {{ number_format($chartConfig['top_products']['min']) }}</span>
                    <span>{{ __('admin/dashboard.charts.axis_step') }}: {{ number_format($chartConfig['top_products']['step']) }}</span>
                    <span>{{ __('admin/dashboard.charts.axis_max') }}: {{ number_format($chartConfig['top_products']['max']) }}</span>
                </div>
            @endif
        </section>

        <section class="flex min-w-0 flex-col bg-white rounded-xl border border-gray-100 shadow-sm p-5" aria-labelledby="dashboard-inventory-title">
            <div class="mb-5">
                <h2 id="dashboard-inventory-title" class="font-semibold text-gray-900">{{ __('admin/dashboard.charts.inventory') }}</h2>
                <p class="text-xs text-gray-500 mt-1">{{ __('admin/dashboard.charts.inventory_hint') }}</p>
            </div>
            <div class="relative h-64 sm:h-72"><canvas id="inventory-chart" role="img" aria-label="{{ __('admin/dashboard.charts.inventory_accessible') }}"></canvas></div>
            <div class="mt-3 flex flex-wrap items-center justify-between gap-x-3 gap-y-1 border-t border-gray-100 pt-3 text-xs text-gray-500">
                <span>{{ __('admin/dashboard.charts.axis_min') }}: {{ number_format($chartConfig['inventory']['min']) }}</span>
                <span>{{ __('admin/dashboard.charts.axis_max') }}: {{ number_format($chartConfig['inventory']['max']) }}</span>
                <span>{{ __('admin/dashboard.charts.total') }}: {{ number_format(array_sum($inventory)) }}</span>
            </div>
            <a href="{{ route('admin.products.index') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
                {{ __('admin/dashboard.charts.manage_products') }}
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </section>
    </div>

    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-7 gap-3 mb-6">
        @foreach ($cards as $card)
            @php [$bg, $fg] = $tones[$card['tone']]; @endphp
            <a href="{{ route($card['route']) }}"
                class="group {{ !empty($card['wide']) ? 'col-span-2' : '' }} bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md hover:border-primary/25 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-9 h-9 shrink-0 rounded-lg flex items-center justify-center {{ $bg }} {{ $fg }}">
                        <i class="fa-solid {{ $card['icon'] }} text-sm"></i>
                    </span>
                    <span class="text-sm font-medium text-gray-500 truncate">
                        {{ __('admin/dashboard.stats.' . $card['key']) }}
                    </span>
                    <i class="fa-solid fa-arrow-right ml-auto text-xs text-gray-300 group-hover:text-primary transition-colors"></i>
                </div>
                <p class="text-2xl lg:text-3xl font-bold text-gray-900 truncate">{{ $card['value'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">{{ __('admin/dashboard.sections.latest_orders') }}</h2>
                <a href="{{ route('admin.orders.index') }}"
                    class="text-sm font-medium text-primary hover:underline">{{ __('common.actions.view_all') }}</a>
            </div>

            @if ($latestOrders->isEmpty())
                <p class="py-14 text-center text-sm text-gray-500">{{ __('admin/dashboard.order.empty') }}</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px]">
                        <thead>
                            <tr class="text-xs font-semibold uppercase text-left text-gray-500 border-b border-gray-100">
                                <th class="py-3 px-5">{{ __('admin/dashboard.order.code') }}</th>
                                <th class="py-3 px-3">{{ __('admin/dashboard.order.customer') }}</th>
                                <th class="py-3 px-3 text-right">{{ __('admin/dashboard.order.total') }}</th>
                                <th class="py-3 px-5 text-center">{{ __('common.labels.status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @foreach ($latestOrders as $order)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-5">
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="font-medium text-primary hover:underline">{{ $order->code }}</a>
                                        <span class="block text-xs text-gray-400">{{ $order->created_at?->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="py-3 px-3 truncate max-w-40">{{ $order->fullname }}</td>
                                    <td class="py-3 px-3 text-right font-medium whitespace-nowrap">
                                        {{ format_price($order->total_amount) }}</td>
                                    <td class="py-3 px-5 text-center">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ \App\Const\OrderConst::statusBadgeClass($order->status) }}">
                                            {{ \App\Const\OrderConst::statusLabel($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">{{ __('admin/dashboard.sections.latest_users') }}</h2>
                <a href="{{ route('admin.users.index') }}"
                    class="text-sm font-medium text-primary hover:underline">{{ __('common.actions.view_all') }}</a>
            </div>

            @if ($latestUsers->isEmpty())
                <p class="py-14 text-center text-sm text-gray-500">{{ __('common.empty.title') }}</p>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach ($latestUsers as $user)
                        <li>
                            <a href="{{ route('admin.users.show', $user->id) }}"
                                class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors">
                                <span
                                    class="w-9 h-9 shrink-0 rounded-full bg-primary-soft text-primary text-sm font-semibold flex items-center justify-center">
                                    {{ Str::upper(Str::substr($user->fullname, 0, 1)) }}
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-medium text-gray-900 truncate">{{ $user->fullname }}</span>
                                    <span class="block text-xs text-gray-500 truncate">{{ $user->email }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
