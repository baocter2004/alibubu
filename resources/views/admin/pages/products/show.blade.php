@extends('admin.layouts.app')

@section('title', __('admin/product.title.show'))

@section('content')
    <div class="w-full bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $product->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $product->sku ?: '-' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.products.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('common.actions.back') }}
                </a>
                <a href="{{ route('shop.show', $product->slug) }}" target="_blank" rel="noopener"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fas fa-arrow-up-right-from-square"></i>
                    {{ __('admin/nav.view_site') }}
                </a>
                <a href="{{ route('admin.products.edit', $product->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-edit"></i>
                    {{ __('common.actions.edit') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div>
                <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center">
                    @if ($product->thumbnail)
                        <img src="{{ Storage::disk('public')->url($product->thumbnail) }}" alt="{{ $product->name }}"
                            class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-box-open text-6xl text-gray-300"></i>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @php
                        $rows = [
                            ['label' => __('admin/product.fields.branch'), 'value' => $product->branch?->name ?? '-'],
                            ['label' => __('admin/product.fields.categories'), 'value' => $product->categories->pluck('name')->implode(', ') ?: '-'],
                            ['label' => __('admin/product.fields.type'), 'value' => __('enum.product.type.' . $product->type)],
                            ['label' => __('admin/product.fields.price'), 'value' => format_price($product->base_price)],
                            ['label' => __('admin/product.fields.sale_price'), 'value' => $product->sale_price ? format_price($product->sale_price) : '-'],
                            ['label' => __('admin/product.fields.views'), 'value' => number_format($product->views)],
                            ['label' => __('common.labels.status'), 'value' => \App\Const\GlobalConst::statusLabel($product->is_active)],
                            ['label' => __('common.labels.created_at'), 'value' => $product->created_at?->format('d/m/Y H:i')],
                        ];
                    @endphp

                    @foreach ($rows as $row)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ $row['label'] }}</dt>
                            <dd class="text-gray-800 font-medium break-words">{{ $row['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                @if ($product->short_descriptions)
                    <div class="mt-5">
                        <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                            {{ __('admin/product.fields.short_descriptions') }}</p>
                        <p class="text-gray-800">{{ $product->short_descriptions }}</p>
                    </div>
                @endif

                @if ($product->descriptions)
                    <div class="mt-5">
                        <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                            {{ __('admin/product.fields.descriptions') }}</p>
                        <p class="text-gray-800 whitespace-pre-line">{{ $product->descriptions }}</p>
                    </div>
                @endif
            </div>
        </div>

        @if ($product->variants->isNotEmpty())
            <div class="mt-8">
                <h2 class="font-semibold text-gray-900 mb-3">{{ __('admin/product.variant.section') }}</h2>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-[640px] w-full">
                        <thead>
                            <tr class="text-xs font-semibold uppercase text-left bg-gray-50 text-gray-600">
                                <th class="px-4 py-3">{{ __('admin/product.fields.sku') }}</th>
                                <th class="px-4 py-3">{{ __('admin/product.fields.attributes') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('admin/product.fields.price') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('admin/product.fields.sale_price') }}</th>
                                <th class="px-4 py-3 text-center">{{ __('common.labels.status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                            @foreach ($product->variants as $variant)
                                <tr>
                                    <td class="px-4 py-3">{{ $variant->sku }}</td>
                                    <td class="px-4 py-3">
                                        {{ $variant->attributeValues->pluck('value')->implode(' / ') ?: '-' }}</td>
                                    <td class="px-4 py-3 text-right">{{ format_price($variant->price) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        {{ $variant->sale_price ? format_price($variant->sale_price) : '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ \App\Const\GlobalConst::statusBadgeClass($variant->is_active) }}">
                                            {{ \App\Const\GlobalConst::statusLabel($variant->is_active) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
