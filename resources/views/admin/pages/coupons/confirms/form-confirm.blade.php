@extends('admin.layouts.app')

@section('title', __('admin/coupon.title.confirm'))

@section('content')
    @php $isPercent = (int) ($data['discount_type'] ?? 0) === \App\Const\CouponConst::PERCENT; @endphp

    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/coupon.title.confirm') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/coupon.subtitle.confirm') }}</p>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            @php
                $rows = [
                    ['label' => __('admin/coupon.fields.code'), 'value' => $data['code'] ?? '-'],
                    ['label' => __('admin/coupon.fields.title'), 'value' => $data['title'] ?? '-'],
                    ['label' => __('admin/coupon.fields.discount_type'), 'value' => \App\Const\CouponConst::typeLabel($data['discount_type'] ?? null)],
                    ['label' => __('admin/coupon.fields.discount_value'), 'value' => $isPercent ? ($data['discount_value'] ?? 0) . '%' : format_price($data['discount_value'] ?? 0)],
                    ['label' => __('admin/coupon.fields.max_discount_value'), 'value' => !empty($data['max_discount_value']) ? format_price($data['max_discount_value']) : '-'],
                    ['label' => __('admin/coupon.fields.min_order_value'), 'value' => format_price($data['min_order_value'] ?? 0)],
                    ['label' => __('admin/coupon.fields.usage_limit'), 'value' => $data['usage_limit'] ?? 0],
                    ['label' => __('common.labels.status'), 'value' => \App\Const\GlobalConst::statusLabel($data['is_active'] ?? null)],
                    ['label' => __('admin/coupon.fields.start_date'), 'value' => $data['start_date'] ?: __('admin/coupon.unlimited')],
                    ['label' => __('admin/coupon.fields.end_date'), 'value' => $data['end_date'] ?: __('admin/coupon.unlimited')],
                    ['label' => __('admin/coupon.fields.valid_categories'), 'value' => !empty($data['valid_categories']) ? collect($data['valid_categories'])->map(fn($id) => $categories[$id] ?? null)->filter()->implode(', ') : __('admin/coupon.all_categories')],
                ];
            @endphp

            @foreach ($rows as $row)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ $row['label'] }}</dt>
                    <dd class="text-gray-800 font-medium break-words">{{ $row['value'] }}</dd>
                </div>
            @endforeach
        </dl>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ !empty($data['id']) ? route('admin.coupons.edit', $data['id']) : route('admin.coupons.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('common.actions.back') }}
            </a>

            <form action="{{ route('admin.coupons.save') }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fa-solid fa-floppy-disk"></i>
                    {{ __('common.actions.save') }}
                </button>
            </form>
        </div>
    </div>
@endsection
