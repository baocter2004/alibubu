@extends('admin.layouts.app')

@section('title', __('admin/order.title.index'))

@section('content')
    @php
        $statCards = [
            ['key' => 'total', 'value' => null, 'icon' => 'fa-receipt', 'class' => 'bg-gray-100 text-gray-600'],
            ['key' => 'pending', 'value' => \App\Const\OrderConst::STATUS_PENDING, 'icon' => 'fa-clock', 'class' => 'bg-amber-100 text-amber-600'],
            ['key' => 'confirmed', 'value' => \App\Const\OrderConst::STATUS_CONFIRMED, 'icon' => 'fa-circle-check', 'class' => 'bg-sky-100 text-sky-600'],
            ['key' => 'shipping', 'value' => \App\Const\OrderConst::STATUS_SHIPPING, 'icon' => 'fa-truck', 'class' => 'bg-indigo-100 text-indigo-600'],
            ['key' => 'completed', 'value' => \App\Const\OrderConst::STATUS_COMPLETED, 'icon' => 'fa-box-open', 'class' => 'bg-green-100 text-green-600'],
            ['key' => 'cancelled', 'value' => \App\Const\OrderConst::STATUS_CANCELLED, 'icon' => 'fa-ban', 'class' => 'bg-red-100 text-red-600'],
        ];
    @endphp

    <div class="mb-6">
        <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/order.title.index') }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/order.subtitle.index') }}</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 mb-6">
        @foreach ($statCards as $card)
            <a href="{{ route('admin.orders.index', $card['value'] ? ['status' => $card['value']] : []) }}"
                class="bg-white rounded-xl shadow-sm border p-4 transition-all hover:shadow-md {{ (string) request('status') === (string) $card['value'] ? 'border-blue-400 ring-1 ring-blue-200' : 'border-gray-100' }}">
                <span class="w-9 h-9 rounded-lg flex items-center justify-center mb-2 {{ $card['class'] }}">
                    <i class="fa-solid {{ $card['icon'] }} text-sm"></i>
                </span>
                <span class="block text-xs text-gray-500">{{ __('admin/order.stats.' . $card['key']) }}</span>
                <span class="block text-xl font-bold text-gray-900">{{ number_format($statistics[$card['key']]) }}</span>
            </a>
        @endforeach
    </div>

    <div class="w-full mb-6 bg-white rounded-lg shadow-lg p-4 md:p-6">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="keyword"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.labels.keyword') }}</label>
                    <input type="search" id="keyword" name="keyword" value="{{ request('keyword') }}"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="status"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/order.fields.status') }}</label>
                    <select id="status" name="status"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('common.labels.all') }}</option>
                        @foreach ($statuses as $key => $value)
                            <option value="{{ $key }}" @selected((string) request('status') === (string) $key)>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="from_date"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/order.fields.from_date') }}</label>
                    <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="to_date"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/order.fields.to_date') }}</label>
                    <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('to_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-magnifying-glass"></i>
                    {{ __('common.actions.search') }}
                </button>
                <a href="{{ route('admin.orders.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-rotate-left"></i>
                    {{ __('common.actions.clear_filter') }}
                </a>
            </div>
        </form>
    </div>

    <div class="w-full bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[980px] w-full table-fixed admin-table">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[15%] px-4 py-3">{{ __('admin/order.fields.code') }}</th>
                        <th class="w-[18%] px-4 py-3">{{ __('admin/order.fields.fullname') }}</th>
                        <th class="w-[13%] px-4 py-3">{{ __('admin/order.fields.phone_number') }}</th>
                        <th class="w-[9%] text-center px-4 py-3">{{ __('admin/order.fields.items_count') }}</th>
                        <th class="w-[14%] text-right px-4 py-3">{{ __('admin/order.fields.total_amount') }}</th>
                        <th class="w-[12%] text-center px-4 py-3">{{ __('admin/order.fields.status') }}</th>
                        <th class="w-[11%] text-center px-4 py-3">{{ __('admin/order.fields.payment') }}</th>
                        <th class="w-[8%] text-center px-4 py-3">{{ __('common.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($orders as $order)
                        <tr class="text-sm text-gray-700 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    class="font-medium text-blue-600 hover:underline">{{ $order->code }}</a>
                                <span
                                    class="block text-xs text-gray-500">{{ $order->created_at?->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="px-4 py-3 truncate">
                                {{ $order->fullname }}
                                <span
                                    class="block text-xs text-gray-500 truncate">{{ $order->user?->email ?? __('admin/order.item.guest') }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $order->phone_number }}</td>
                            <td class="text-center px-4 py-3">{{ $order->items_count }}</td>
                            <td class="text-right px-4 py-3 font-medium whitespace-nowrap">
                                {{ format_price($order->total_amount) }}</td>
                            <td class="text-center px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ \App\Const\OrderConst::statusBadgeClass($order->status) }}">
                                    {{ \App\Const\OrderConst::statusLabel($order->status) }}
                                </span>
                            </td>
                            <td class="text-center px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->is_paid ? 'text-green-600 bg-green-100' : 'text-amber-600 bg-amber-100' }}">
                                    {{ $order->is_paid ? __('admin/order.payment.paid') : __('admin/order.payment.unpaid') }}
                                </span>
                            </td>
                            <td class="text-center px-4 py-3">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    class="text-blue-500 hover:text-blue-700" title="{{ __('common.actions.view') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center text-gray-500">
                                <i class="fas fa-receipt text-4xl text-gray-300 block mb-3"></i>
                                <p class="font-medium text-gray-700">{{ __('common.empty.title') }}</p>
                                <p class="text-sm">{{ __('common.empty.description') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $orders->withQueryString()])
    </div>
@endsection
