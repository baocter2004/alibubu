@extends('admin.layouts.app')

@section('title', __('admin/dashboard.title'))

@section('content')
    @php
        $admin = Auth::guard('admin')->user();
        $cards = [
            ['key' => 'users', 'icon' => 'fa-users', 'color' => 'blue', 'value' => number_format($stats['users']), 'route' => 'admin.users.index'],
            ['key' => 'products', 'icon' => 'fa-boxes', 'color' => 'purple', 'value' => number_format($stats['products']), 'route' => null],
            ['key' => 'categories', 'icon' => 'fa-layer-group', 'color' => 'amber', 'value' => number_format($stats['categories']), 'route' => null],
            ['key' => 'branches', 'icon' => 'fa-store', 'color' => 'emerald', 'value' => number_format($stats['branches']), 'route' => 'admin.branches.index'],
            ['key' => 'orders', 'icon' => 'fa-cart-shopping', 'color' => 'sky', 'value' => number_format($stats['orders']), 'route' => null],
            ['key' => 'revenue', 'icon' => 'fa-sack-dollar', 'color' => 'rose', 'value' => format_price($stats['revenue']), 'route' => null],
        ];
        $palette = [
            'blue' => 'bg-blue-100 text-blue-600',
            'purple' => 'bg-purple-100 text-purple-600',
            'amber' => 'bg-amber-100 text-amber-600',
            'emerald' => 'bg-emerald-100 text-emerald-600',
            'sky' => 'bg-sky-100 text-sky-600',
            'rose' => 'bg-rose-100 text-rose-600',
        ];
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">
            {{ __('admin/dashboard.welcome', ['name' => $admin?->name]) }}
        </h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('admin/dashboard.subtitle') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
        @foreach ($cards as $card)
            @php
                $cardClass =
                    'flex items-center gap-4 bg-white rounded-xl shadow-sm border border-gray-100 p-5' .
                    ($card['route'] ? ' hover:shadow-md hover:border-blue-200 transition-all' : '');
            @endphp

            @if ($card['route'])
                <a href="{{ route($card['route']) }}" class="{{ $cardClass }}">
                @else
                    <div class="{{ $cardClass }}">
            @endif

            <span class="w-12 h-12 shrink-0 rounded-xl flex items-center justify-center {{ $palette[$card['color']] }}">
                <i class="fa-solid {{ $card['icon'] }} text-lg"></i>
            </span>
            <span class="min-w-0">
                <span class="block text-sm text-gray-500">{{ __('admin/dashboard.stats.' . $card['key']) }}</span>
                <span class="block text-2xl font-bold text-gray-900 truncate">{{ $card['value'] }}</span>
            </span>

            @if ($card['route'])
                </a>
            @else
                </div>
            @endif
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-semibold text-gray-900 mb-4">{{ __('admin/dashboard.sections.latest_orders') }}</h2>

            @if ($latestOrders->isEmpty())
                <p class="py-10 text-center text-sm text-gray-500">{{ __('admin/dashboard.order.empty') }}</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px]">
                        <thead>
                            <tr class="text-xs font-semibold uppercase text-left text-gray-500 border-b border-gray-200">
                                <th class="py-2 pr-3">{{ __('admin/dashboard.order.code') }}</th>
                                <th class="py-2 px-3">{{ __('admin/dashboard.order.customer') }}</th>
                                <th class="py-2 px-3 text-right">{{ __('admin/dashboard.order.total') }}</th>
                                <th class="py-2 px-3 text-center">{{ __('common.labels.status') }}</th>
                                <th class="py-2 pl-3">{{ __('admin/dashboard.order.created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @foreach ($latestOrders as $order)
                                <tr>
                                    <td class="py-3 pr-3 font-medium text-gray-900">{{ $order->code }}</td>
                                    <td class="py-3 px-3 truncate">{{ $order->fullname }}</td>
                                    <td class="py-3 px-3 text-right whitespace-nowrap">
                                        {{ format_price($order->total_amount) }}</td>
                                    <td class="py-3 px-3 text-center">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->is_paid ? 'text-green-600 bg-green-100' : 'text-amber-600 bg-amber-100' }}">
                                            {{ $order->is_paid ? __('admin/dashboard.order.paid') : __('admin/dashboard.order.unpaid') }}
                                        </span>
                                    </td>
                                    <td class="py-3 pl-3 whitespace-nowrap">{{ $order->created_at?->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-semibold text-gray-900 mb-4">{{ __('admin/dashboard.sections.latest_users') }}</h2>

            @if ($latestUsers->isEmpty())
                <p class="py-10 text-center text-sm text-gray-500">{{ __('common.empty.title') }}</p>
            @else
                <ul class="space-y-3">
                    @foreach ($latestUsers as $user)
                        <li class="flex items-center gap-3">
                            <span
                                class="w-9 h-9 shrink-0 rounded-full bg-blue-100 text-blue-600 font-semibold flex items-center justify-center">
                                {{ Str::upper(Str::substr($user->fullname, 0, 1)) }}
                            </span>
                            <span class="min-w-0 flex-1">
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                    class="block text-sm font-medium text-gray-900 truncate hover:text-blue-600 transition-colors">
                                    {{ $user->fullname }}
                                </a>
                                <span class="block text-xs text-gray-500 truncate">{{ $user->email }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
