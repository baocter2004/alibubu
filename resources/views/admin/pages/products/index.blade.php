@extends('admin.layouts.app')

@section('title', __('admin/product.title.index'))

@section('content')
    <div class="w-full mb-6 bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/product.title.index') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/product.subtitle.index') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.products.trash') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-trash"></i>
                    <span class="hidden sm:inline">{{ __('common.labels.trash') }}</span>
                </a>
                <a href="{{ route('admin.products.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-plus"></i>
                    {{ __('common.actions.create') }}
                </a>
            </div>
        </div>

        <form action="{{ route('admin.products.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="keyword"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.labels.keyword') }}</label>
                    <input type="search" id="keyword" name="keyword" value="{{ request('keyword') }}"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="branch_id"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/product.fields.branch') }}</label>
                    <select id="branch_id" name="branch_id"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('common.labels.all') }}</option>
                        @foreach ($branches as $id => $name)
                            <option value="{{ $id }}" @selected((string) request('branch_id') === (string) $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="category_id"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/product.fields.categories') }}</label>
                    <select id="category_id" name="category_id"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('common.labels.all') }}</option>
                        @foreach ($categories as $id => $name)
                            <option value="{{ $id }}" @selected((string) request('category_id') === (string) $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="is_active"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.labels.status') }}</label>
                    <select id="is_active" name="is_active"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('common.labels.all') }}</option>
                        @foreach ($statuses as $key => $value)
                            <option value="{{ $key }}" @selected(request('is_active') !== null && (string) request('is_active') === (string) $key)>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-magnifying-glass"></i>
                    {{ __('common.actions.search') }}
                </button>
                <a href="{{ route('admin.products.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-rotate-left"></i>
                    {{ __('common.actions.clear_filter') }}
                </a>
            </div>
        </form>
    </div>

    <div class="w-full bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[1100px] w-full table-fixed admin-table">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[6%] text-center px-4 py-3">{{ __('common.labels.id') }}</th>
                        <th class="w-[8%] text-center px-4 py-3">{{ __('common.labels.image') }}</th>
                        <th class="w-[22%] px-4 py-3">{{ __('admin/product.fields.name') }}</th>
                        <th class="w-[12%] px-4 py-3">{{ __('admin/product.fields.branch') }}</th>
                        <th class="w-[11%] px-4 py-3">{{ __('admin/product.fields.type') }}</th>
                        <th class="w-[13%] text-right px-4 py-3">{{ __('admin/product.fields.price') }}</th>
                        <th class="w-[8%] text-center px-4 py-3">{{ __('admin/product.fields.views') }}</th>
                        <th class="w-[10%] text-center px-4 py-3">{{ __('common.labels.status') }}</th>
                        <th class="w-[10%] text-center px-4 py-3">{{ __('common.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($products as $product)
                        <tr class="text-sm text-gray-700 transition-colors">
                            <td class="text-center px-4 py-3">{{ $product->id }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="block w-12 h-12 mx-auto bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                                    @if ($product->thumbnail)
                                        <img src="{{ Storage::disk('public')->url($product->thumbnail) }}"
                                            alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-box-open text-gray-300"></i>
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="block font-medium text-gray-900 truncate">{{ $product->name }}</span>
                                <span class="block text-xs text-gray-500 truncate">{{ $product->sku ?: '-' }}</span>
                            </td>
                            <td class="px-4 py-3 truncate">{{ $product->branch?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ __('enum.product.type.' . $product->type) }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <span class="block font-medium">{{ format_price($product->effective_price) }}</span>
                                @if ($product->discount_percent > 0)
                                    <span
                                        class="block text-xs text-gray-400 line-through">{{ format_price($product->base_price) }}</span>
                                @endif
                            </td>
                            <td class="text-center px-4 py-3">{{ number_format($product->views) }}</td>
                            <td class="text-center px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ \App\Const\GlobalConst::statusBadgeClass($product->is_active) }}">
                                    {{ \App\Const\GlobalConst::statusLabel($product->is_active) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.products.show', $product->id) }}"
                                        class="text-blue-500 hover:text-blue-700" title="{{ __('common.actions.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                        class="text-yellow-500 hover:text-yellow-700"
                                        title="{{ __('common.actions.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
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
                            <td colspan="9" class="px-4 py-16 text-center text-gray-500">
                                <i class="fas fa-boxes text-4xl text-gray-300 block mb-3"></i>
                                <p class="font-medium text-gray-700">{{ __('common.empty.title') }}</p>
                                <p class="text-sm">{{ __('common.empty.description') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $products->withQueryString()])
    </div>
@endsection
