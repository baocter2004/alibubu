@php
    $values = ! empty($coupon) ? array_merge($coupon->toArray(), [
        'min_order_value' => $coupon->restriction?->min_order_value,
        'max_discount_value' => $coupon->restriction?->max_discount_value,
        'valid_categories' => $coupon->restriction?->valid_categories ?? [],
        'start_date' => $coupon->start_date?->format('Y-m-d'),
        'end_date' => $coupon->end_date?->format('Y-m-d'),
    ]) : ($data ?? []);
    $selectedCategories = old('valid_categories', $values['valid_categories'] ?? []);
@endphp

<form action="{{ $formAction }}" method="POST" class="space-y-6">
    @csrf

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="px-5 py-4 border-b border-gray-100 bg-gray-50/60">
            <h2 class="font-semibold text-gray-900">{{ __('admin/coupon.sections.general') }}</h2>
        </header>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                @include('components.input', [
                    'label' => __('admin/coupon.fields.code'),
                    'name' => 'code',
                    'required' => true,
                    'icon' => 'ticket',
                    'value' => $values['code'] ?? '',
                    'class' => 'uppercase',
                ])
                <p class="text-xs text-gray-500 mt-1.5">{{ __('admin/coupon.hints.code') }}</p>
            </div>

            @include('components.input', [
                'label' => __('admin/coupon.fields.title'),
                'name' => 'title',
                'required' => true,
                'icon' => 'heading',
                'value' => $values['title'] ?? '',
            ])

            <div class="md:col-span-2">
                @include('components.input', [
                    'label' => __('admin/coupon.fields.description'),
                    'name' => 'description',
                    'icon' => 'align-left',
                    'value' => $values['description'] ?? '',
                ])
            </div>
        </div>
    </section>

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="px-5 py-4 border-b border-gray-100 bg-gray-50/60">
            <h2 class="font-semibold text-gray-900">{{ __('admin/coupon.sections.discount') }}</h2>
        </header>

        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-5">
            @include('components.select', [
                'label' => __('admin/coupon.fields.discount_type'),
                'name' => 'discount_type',
                'required' => true,
                'options' => \App\Const\CouponConst::types(),
                'value' => (string) ($values['discount_type'] ?? \App\Const\CouponConst::FIX_AMOUNT),
            ])

            <div>
                @include('components.input', [
                    'label' => __('admin/coupon.fields.discount_value'),
                    'name' => 'discount_value',
                    'type' => 'number',
                    'required' => true,
                    'icon' => 'percent',
                    'value' => $values['discount_value'] ?? '',
                ])
                <p class="text-xs text-gray-500 mt-1.5">{{ __('admin/coupon.hints.discount_value') }}</p>
            </div>

            <div>
                @include('components.input', [
                    'label' => __('admin/coupon.fields.max_discount_value'),
                    'name' => 'max_discount_value',
                    'type' => 'number',
                    'icon' => 'circle-down',
                    'value' => $values['max_discount_value'] ?? '',
                ])
                <p class="text-xs text-gray-500 mt-1.5">{{ __('admin/coupon.hints.max_discount_value') }}</p>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="px-5 py-4 border-b border-gray-100 bg-gray-50/60">
            <h2 class="font-semibold text-gray-900">{{ __('admin/coupon.sections.conditions') }}</h2>
        </header>

        <div class="p-5 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @include('components.input', [
                    'label' => __('admin/coupon.fields.min_order_value'),
                    'name' => 'min_order_value',
                    'type' => 'number',
                    'icon' => 'cart-shopping',
                    'value' => $values['min_order_value'] ?? 0,
                ])

                @include('components.input', [
                    'label' => __('admin/coupon.fields.usage_limit'),
                    'name' => 'usage_limit',
                    'type' => 'number',
                    'required' => true,
                    'icon' => 'hashtag',
                    'value' => $values['usage_limit'] ?? 100,
                ])

                @include('components.select', [
                    'label' => __('admin/coupon.fields.is_active'),
                    'name' => 'is_active',
                    'required' => true,
                    'options' => \App\Const\GlobalConst::statuses(),
                    'value' => (string) ($values['is_active'] ?? \App\Const\GlobalConst::IS_ACTIVE),
                ])
            </div>

            <div>
                <label class="flex items-center gap-x-2 text-sm font-medium text-blue-500 mb-2">
                    <i class="fa-solid fa-layer-group"></i>
                    {{ __('admin/coupon.fields.valid_categories') }}
                </label>

                <div
                    class="grid grid-cols-2 md:grid-cols-4 gap-2 max-h-40 overflow-y-auto p-3 border rounded-lg {{ $errors->has('valid_categories') ? 'is-invalid' : 'border-gray-300' }}">
                    @foreach ($categories as $id => $name)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="valid_categories[]" value="{{ $id }}"
                                @checked(in_array($id, $selectedCategories, false))
                                class="h-4 w-4 rounded accent-blue-500">
                            <span class="text-sm text-gray-700 truncate">{{ $name }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-1.5">{{ __('admin/coupon.hints.valid_categories') }}</p>
                @error('valid_categories')
                    <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="px-5 py-4 border-b border-gray-100 bg-gray-50/60">
            <h2 class="font-semibold text-gray-900">{{ __('admin/coupon.sections.schedule') }}</h2>
        </header>

        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @include('components.input', [
                    'label' => __('admin/coupon.fields.start_date'),
                    'name' => 'start_date',
                    'type' => 'date',
                    'icon' => 'calendar',
                    'value' => $values['start_date'] ?? '',
                ])

                @include('components.input', [
                    'label' => __('admin/coupon.fields.end_date'),
                    'name' => 'end_date',
                    'type' => 'date',
                    'icon' => 'calendar-check',
                    'value' => $values['end_date'] ?? '',
                ])
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ __('admin/coupon.hints.schedule') }}</p>
        </div>
    </section>

    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('admin.coupons.index') }}"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            {{ __('common.actions.back') }}
        </a>
        <button type="submit"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
            <i class="fa-solid fa-arrow-right"></i>
            {{ __('common.actions.confirm') }}
        </button>
    </div>
</form>
