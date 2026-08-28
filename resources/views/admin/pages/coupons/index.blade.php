@extends('admin.layouts.app')

@section('title', __('admin/coupon.title.index'))

@section('content')
    <div class="w-full mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/coupon.title.index') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/coupon.subtitle.index') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.coupons.trash') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-trash"></i>
                    <span class="hidden sm:inline">{{ __('common.labels.trash') }}</span>
                </a>
                <a href="{{ route('admin.coupons.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-plus"></i>
                    {{ __('common.actions.create') }}
                </a>
            </div>
        </div>

        <form action="{{ route('admin.coupons.index') }}" method="GET"
            class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-2">
                <label for="keyword"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.labels.keyword') }}</label>
                <input type="search" id="keyword" name="keyword" value="{{ request('keyword') }}"
                    class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="discount_type"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/coupon.fields.discount_type') }}</label>
                <select id="discount_type" name="discount_type"
                    class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('common.labels.all') }}</option>
                    @foreach ($types as $key => $label)
                        <option value="{{ $key }}" @selected((string) request('discount_type') === (string) $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <div class="flex-1">
                    <label for="is_active"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.labels.status') }}</label>
                    <select id="is_active" name="is_active"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('common.labels.all') }}</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected(request('is_active') !== null && (string) request('is_active') === (string) $key)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="self-end px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-magnifying-glass"></i>
                </button>
                <a href="{{ route('admin.coupons.index') }}"
                    class="self-end px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[980px] w-full table-fixed">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[16%] px-4 py-3">{{ __('admin/coupon.fields.code') }}</th>
                        <th class="w-[20%] px-4 py-3">{{ __('admin/coupon.fields.title') }}</th>
                        <th class="w-[13%] px-4 py-3">{{ __('admin/coupon.fields.discount_type') }}</th>
                        <th class="w-[13%] text-right px-4 py-3">{{ __('admin/coupon.fields.discount_value') }}</th>
                        <th class="w-[12%] text-center px-4 py-3">{{ __('admin/coupon.fields.usage') }}</th>
                        <th class="w-[14%] px-4 py-3">{{ __('admin/coupon.fields.end_date') }}</th>
                        <th class="w-[12%] text-center px-4 py-3">{{ __('common.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($coupons as $coupon)
                        <tr class="text-sm text-gray-700 hover:bg-blue-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-mono font-semibold text-gray-900">{{ $coupon->code }}</span>
                                <span
                                    class="block mt-1 px-2 py-0.5 w-fit text-[11px] font-semibold rounded-full {{ \App\Const\GlobalConst::statusBadgeClass($coupon->is_active) }}">
                                    {{ \App\Const\GlobalConst::statusLabel($coupon->is_active) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 truncate">{{ $coupon->title }}</td>
                            <td class="px-4 py-3">{{ \App\Const\CouponConst::typeLabel($coupon->discount_type) }}</td>
                            <td class="px-4 py-3 text-right font-medium whitespace-nowrap">
                                {{ $coupon->discount_type === \App\Const\CouponConst::PERCENT
                                    ? rtrim(rtrim(number_format($coupon->discount_value, 2), '0'), '.') . '%'
                                    : format_price($coupon->discount_value) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-gray-900 font-medium">{{ $coupon->usage_count }}</span>
                                <span class="text-gray-400">/ {{ $coupon->usage_limit }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $coupon->end_date?->format('d/m/Y') ?? __('admin/coupon.unlimited') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.coupons.show', $coupon->id) }}"
                                        class="text-blue-500 hover:text-blue-700" title="{{ __('common.actions.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                                        class="text-yellow-500 hover:text-yellow-700" title="{{ __('common.actions.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST"
                                        data-confirm="{{ __('common.confirm.delete_text') }}"
                                        data-confirm-title="{{ __('common.confirm.delete_title') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700"
                                            title="{{ __('common.actions.delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center text-gray-500">
                                <i class="fas fa-ticket text-4xl text-gray-300 block mb-3"></i>
                                <p class="font-medium text-gray-700">{{ __('common.empty.title') }}</p>
                                <p class="text-sm">{{ __('common.empty.description') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $coupons->withQueryString()])
    </div>
@endsection
