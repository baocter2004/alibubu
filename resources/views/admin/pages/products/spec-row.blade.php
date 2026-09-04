@php
    $idx = $index ?? 'INDEX';
    $spec = $spec ?? [];
@endphp

<div class="spec-item flex flex-col sm:flex-row gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3" data-index="{{ $idx }}">
    <input type="hidden" name="specifications[{{ $idx }}][id]" value="{{ $spec['id'] ?? '' }}">

    <input type="text" name="specifications[{{ $idx }}][group]" value="{{ $spec['group'] ?? '' }}"
        placeholder="{{ __('admin/product.spec.group_placeholder') }}"
        class="sm:w-40 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/30">

    <div class="flex-1">
        <input type="text" name="specifications[{{ $idx }}][name]" value="{{ $spec['name'] ?? '' }}"
            placeholder="{{ __('admin/product.spec.name_placeholder') }}"
            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/30 {{ $errors->has("specifications.$idx.name") ? 'is-invalid' : 'border-gray-300' }}">
        @error("specifications.$idx.name")
            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex-1">
        <input type="text" name="specifications[{{ $idx }}][value]" value="{{ $spec['value'] ?? '' }}"
            placeholder="{{ __('admin/product.spec.value_placeholder') }}"
            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/30 {{ $errors->has("specifications.$idx.value") ? 'is-invalid' : 'border-gray-300' }}">
        @error("specifications.$idx.value")
            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <button type="button" class="remove-spec-btn self-start sm:self-center text-red-500 hover:text-red-700 px-2"
        title="{{ __('admin/product.spec.remove') }}">
        <i class="fa-solid fa-trash-can"></i>
    </button>
</div>
