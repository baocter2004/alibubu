@php
    $idx = $index ?? 'INDEX';
    $promotion = $promotion ?? [];
@endphp

<div class="promotion-item flex flex-col sm:flex-row gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3"
    data-index="{{ $idx }}">
    <input type="hidden" name="promotions[{{ $idx }}][id]" value="{{ $promotion['id'] ?? '' }}">

    <input type="text" name="promotions[{{ $idx }}][icon]" value="{{ $promotion['icon'] ?? '' }}"
        placeholder="{{ __('admin/product.promotion.icon_placeholder') }}"
        class="sm:w-44 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/30">

    <div class="flex-1">
        <input type="text" name="promotions[{{ $idx }}][content]" value="{{ $promotion['content'] ?? '' }}"
            placeholder="{{ __('admin/product.promotion.content_placeholder') }}"
            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/30 {{ $errors->has("promotions.$idx.content") ? 'is-invalid' : 'border-gray-300' }}">
        @error("promotions.$idx.content")
            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <button type="button" class="remove-promotion-btn self-start sm:self-center text-red-500 hover:text-red-700 px-2"
        title="{{ __('admin/product.promotion.remove') }}">
        <i class="fa-solid fa-trash-can"></i>
    </button>
</div>
