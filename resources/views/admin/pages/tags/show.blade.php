@extends('admin.layouts.app')

@section('title', __('admin/tag.title.show'))

@section('content')
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $tag->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $tag->slug }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.tags.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('common.actions.back') }}
                </a>
                <a href="{{ route('admin.tags.edit', $tag->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-edit"></i>
                    {{ __('common.actions.edit') }}
                </a>
            </div>
        </div>

        <h2 class="font-semibold text-gray-900 mb-3">
            {{ __('admin/tag.fields.products_count') }} ({{ $tag->products_count }})
        </h2>

        @if ($tag->products->isEmpty())
            <p class="py-10 text-center text-sm text-gray-500">{{ __('common.empty.title') }}</p>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($tag->products as $product)
                    <li>
                        <a href="{{ route('admin.products.show', $product->id) }}"
                            class="flex items-center gap-3 py-3 hover:bg-gray-50 transition-colors px-2 -mx-2 rounded-lg">
                            <span class="w-11 h-11 shrink-0 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                                @if ($product->thumbnail)
                                    <img src="{{ Storage::disk('public')->url($product->thumbnail) }}"
                                        alt="{{ $product->name }}" class="w-full h-full object-contain p-1">
                                @else
                                    <i class="fa-solid fa-box-open text-gray-300"></i>
                                @endif
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-medium text-gray-900 truncate">{{ $product->name }}</span>
                                <span class="block text-xs text-gray-500">{{ format_price($product->effective_price) }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
