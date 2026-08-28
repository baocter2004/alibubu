@extends('admin.layouts.app')

@section('title', __('admin/coupon.title.show'))

@section('content')
    @php
        $isPercent = $coupon->discount_type === \App\Const\CouponConst::PERCENT;
        $validCategories = $coupon->restriction?->valid_categories ?? [];
    @endphp

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <div>
                    <h1 class="text-xl md:text-2xl font-semibold text-gray-900 font-mono">{{ $coupon->code }}</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $coupon->title }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.coupons.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('common.actions.back') }}
                    </a>
                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition-colors">
                        <i class="fas fa-edit"></i>
                        {{ __('common.actions.edit') }}
                    </a>
                </div>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $rows = [
                        ['label' => __('admin/coupon.fields.discount_type'), 'value' => \App\Const\CouponConst::typeLabel($coupon->discount_type)],
                        ['label' => __('admin/coupon.fields.discount_value'), 'value' => $isPercent ? rtrim(rtrim(number_format($coupon->discount_value, 2), '0'), '.') . '%' : format_price($coupon->discount_value)],
                        ['label' => __('admin/coupon.fields.max_discount_value'), 'value' => $coupon->restriction?->max_discount_value ? format_price($coupon->restriction->max_discount_value) : '-'],
                        ['label' => __('admin/coupon.fields.min_order_value'), 'value' => format_price($coupon->restriction?->min_order_value ?? 0)],
                        ['label' => __('admin/coupon.fields.usage'), 'value' => $coupon->usage_count . ' / ' . $coupon->usage_limit],
                        ['label' => __('common.labels.status'), 'value' => \App\Const\GlobalConst::statusLabel($coupon->is_active)],
                        ['label' => __('admin/coupon.fields.start_date'), 'value' => $coupon->start_date?->format('d/m/Y') ?? __('admin/coupon.unlimited')],
                        ['label' => __('admin/coupon.fields.end_date'), 'value' => $coupon->end_date?->format('d/m/Y') ?? __('admin/coupon.unlimited')],
                        ['label' => __('admin/coupon.fields.valid_categories'), 'value' => $validCategories ? collect($validCategories)->map(fn($id) => $categories[$id] ?? null)->filter()->implode(', ') : __('admin/coupon.all_categories')],
                    ];
                @endphp

                @foreach ($rows as $row)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ $row['label'] }}</dt>
                        <dd class="text-gray-800 font-medium break-words">{{ $row['value'] }}</dd>
                    </div>
                @endforeach
            </dl>

            @if ($coupon->description)
                <div class="mt-5">
                    <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ __('admin/coupon.fields.description') }}</p>
                    <p class="text-gray-800">{{ $coupon->description }}</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
            <h2 class="font-semibold text-gray-900 mb-4">{{ __('admin/coupon.fields.users') }}</h2>

            @if ($coupon->users->isEmpty())
                <p class="py-8 text-center text-sm text-gray-500">{{ __('common.empty.title') }}</p>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach ($coupon->users as $user)
                        <li class="flex items-center gap-3 py-3">
                            <span
                                class="w-9 h-9 shrink-0 rounded-full bg-blue-50 text-blue-600 text-sm font-semibold flex items-center justify-center">
                                {{ Str::upper(Str::substr($user->fullname, 0, 1)) }}
                            </span>
                            <span class="min-w-0 flex-1">
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                    class="block text-sm font-medium text-gray-900 truncate hover:text-blue-600 transition-colors">
                                    {{ $user->fullname }}
                                </a>
                                <span class="block text-xs text-gray-500 truncate">{{ $user->email }}</span>
                            </span>
                            <span class="text-xs text-gray-500 whitespace-nowrap">
                                {{ $user->pivot->used_at ? \Illuminate\Support\Carbon::parse($user->pivot->used_at)->format('d/m/Y H:i') : '-' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
