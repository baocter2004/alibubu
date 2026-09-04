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

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
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
