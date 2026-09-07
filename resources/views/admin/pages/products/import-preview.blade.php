@extends('admin.layouts.app')

@section('title', __('admin/product.import.preview_title'))

@section('content')
    @include('admin.partials.page-header', [
        'title' => __('admin/product.import.preview_title'),
        'subtitle' => __('admin/product.import.preview_subtitle'),
        'crumbs' => [
            ['label' => __('admin/product.title.index'), 'url' => route('admin.products.index')],
            ['label' => __('admin/product.import.preview_title')],
        ],
    ])

    <div class="w-full bg-white rounded-lg shadow-lg p-4 md:p-6 mb-6">
        <p class="text-sm text-gray-500 mb-4">
            {{ __('admin/product.import.preview_file') }}:
            <span class="font-medium text-gray-900">{{ $filename }}</span>
        </p>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => __('admin/product.import.preview_create'), 'value' => $preview['counts']['create'], 'class' => 'bg-emerald-50 text-emerald-700', 'icon' => 'fa-circle-plus'],
                ['label' => __('admin/product.import.preview_update'), 'value' => $preview['counts']['update'], 'class' => 'bg-amber-50 text-amber-700', 'icon' => 'fa-pen-to-square'],
                ['label' => __('admin/product.import.preview_variants'), 'value' => $preview['counts']['variants'], 'class' => 'bg-sky-50 text-sky-700', 'icon' => 'fa-sliders'],
                ['label' => __('admin/product.import.preview_specifications'), 'value' => $preview['counts']['specifications'], 'class' => 'bg-gray-100 text-gray-700', 'icon' => 'fa-list-check'],
            ] as $card)
                <div class="flex items-center gap-3 rounded-lg border border-gray-200 p-4">
                    <span class="w-10 h-10 shrink-0 rounded-lg flex items-center justify-center {{ $card['class'] }}">
                        <i class="fa-solid {{ $card['icon'] }}"></i>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-xl font-semibold text-gray-900 tabular">{{ number_format($card['value']) }}</span>
                        <span class="block text-xs text-gray-500">{{ $card['label'] }}</span>
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="w-full bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="w-full overflow-x-auto rounded-lg border border-gray-200 mb-6">
            <table class="min-w-[860px] w-full table-fixed admin-table">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[16%] px-4 py-3">{{ __('admin/product.fields.sku') }}</th>
                        <th class="w-[30%] px-4 py-3">{{ __('admin/product.fields.name') }}</th>
                        <th class="w-[16%] px-4 py-3">{{ __('admin/product.fields.branch') }}</th>
                        <th class="w-[14%] text-right px-4 py-3">{{ __('admin/product.fields.price') }}</th>
                        <th class="w-[10%] text-center px-4 py-3">{{ __('admin/product.fields.stock') }}</th>
                        <th class="w-[14%] text-center px-4 py-3">{{ __('admin/product.import.preview_action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($preview['rows'] as $row)
                        <tr class="text-sm text-gray-700">
                            <td class="px-4 py-3 font-mono text-xs truncate">{{ $row['sku'] }}</td>
                            <td class="px-4 py-3 truncate">
                                {{ $row['name'] }}
                                @if ($row['variants'] > 0)
                                    <span class="ml-1 text-xs text-gray-400">({{ $row['variants'] }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 truncate">{{ $row['brand'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">{{ format_price($row['price'] ?? 0) }}</td>
                            <td class="px-4 py-3 text-center tabular">{{ number_format($row['stock'] ?? 0) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $row['is_update'] ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $row['is_update'] ? __('admin/product.import.preview_existing') : __('admin/product.import.preview_new') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('admin.products.import.store') }}" method="POST" data-submit-once>
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors">
                    <i class="fa-solid fa-circle-check"></i>
                    <span data-submit-label>{{ __('admin/product.import.preview_confirm') }}</span>
                </button>
            </form>

            <a href="{{ route('admin.products.import') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('admin/product.import.preview_cancel') }}
            </a>
        </div>
    </div>
@endsection
