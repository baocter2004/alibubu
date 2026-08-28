@extends('admin.layouts.app')

@section('title', __('admin/product.title.confirm'))

@section('content')
    @php
        $isVariable = (int) ($data['type'] ?? \App\Const\ProductConst::SINGLE) === \App\Const\ProductConst::VARIANT;
        $attributeLabels = $attributeGroups->flatMap(fn($values) => $values)->toArray();
    @endphp

    <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/product.title.confirm') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/product.subtitle.confirm') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div>
                <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center">
                    @if (! empty($data['thumbnail']))
                        <img src="{{ Storage::disk('public')->url($data['thumbnail']) }}"
                            alt="{{ $data['name'] ?? '' }}" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-box-open text-6xl text-gray-300"></i>
                    @endif
                </div>
            </div>

            <dl class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                    $rows = [
                        ['label' => __('admin/product.fields.name'), 'value' => $data['name'] ?? '-'],
                        ['label' => __('admin/product.fields.slug'), 'value' => $data['slug'] ?? '-'],
                        ['label' => __('admin/product.fields.sku'), 'value' => $data['sku'] ?: '-'],
                        ['label' => __('admin/product.fields.branch'), 'value' => $branches[$data['branch_id'] ?? null] ?? '-'],
                        ['label' => __('admin/product.fields.categories'), 'value' => collect($data['category_ids'] ?? [])->map(fn($id) => $categories[$id] ?? null)->filter()->implode(', ') ?: '-'],
                        ['label' => __('admin/product.fields.type'), 'value' => __('enum.product.type.' . ($data['type'] ?? 0))],
                        ['label' => __('admin/product.fields.price'), 'value' => $isVariable ? '-' : format_price($data['price'] ?? 0)],
                        ['label' => __('admin/product.fields.sale_price'), 'value' => ! $isVariable && ! empty($data['sale_price']) ? format_price($data['sale_price']) : '-'],
                        ['label' => __('admin/product.fields.is_featured'), 'value' => !empty($data['is_featured']) ? __('common.labels.yes') : __('common.labels.no')],
                        ['label' => __('admin/product.fields.is_trending'), 'value' => !empty($data['is_trending']) ? __('common.labels.yes') : __('common.labels.no')],
                        ['label' => __('common.labels.status'), 'value' => \App\Const\GlobalConst::statusLabel($data['is_active'] ?? null)],
                    ];
                @endphp

                @foreach ($rows as $row)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ $row['label'] }}</dt>
                        <dd class="text-gray-800 font-medium break-words">{{ $row['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        @if (! empty($data['short_descriptions']))
            <div class="mb-5">
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                    {{ __('admin/product.fields.short_descriptions') }}</p>
                <p class="text-gray-800">{{ $data['short_descriptions'] }}</p>
            </div>
        @endif

        @if (! empty($data['descriptions']))
            <div class="mb-5">
                <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                    {{ __('admin/product.fields.descriptions') }}</p>
                <p class="text-gray-800 whitespace-pre-line">{{ $data['descriptions'] }}</p>
            </div>
        @endif

        @if ($isVariable && ! empty($data['variants']))
            <div class="mb-8">
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
                            @foreach ($data['variants'] as $variant)
                                <tr>
                                    <td class="px-4 py-3">{{ $variant['sku'] ?: __('common.labels.none') }}</td>
                                    <td class="px-4 py-3">
                                        {{ collect($variant['attribute_value_ids'] ?? [])->map(fn($id) => $attributeLabels[$id] ?? null)->filter()->implode(' / ') ?: '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">{{ format_price($variant['price'] ?? 0) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        {{ ! empty($variant['sale_price']) ? format_price($variant['sale_price']) : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ \App\Const\GlobalConst::statusBadgeClass(!empty($variant['is_active'])) }}">
                                            {{ \App\Const\GlobalConst::statusLabel(!empty($variant['is_active'])) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ !empty($data['id']) ? route('admin.products.edit', $data['id']) : route('admin.products.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('common.actions.back') }}
            </a>

            <form action="{{ route('admin.products.save') }}" method="POST">
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
