@php
    $idx = $index ?? 'INDEX';
    $variant = $variant ?? [];
@endphp

<div class="variant-item bg-gray-50 border border-gray-200 rounded-lg p-4 relative" data-index="{{ $idx }}">
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm font-semibold text-gray-700">
            <i class="fa-solid fa-layer-group text-primary mr-1.5"></i>
            <span class="variant-title">{{ __('admin/product.fields.variant_number', ['number' => is_numeric($idx) ? $idx + 1 : 1]) }}</span>
        </p>
        <button type="button"
            class="remove-variant-btn text-red-500 hover:text-red-700 text-sm inline-flex items-center gap-1.5"
            title="{{ __('admin/product.variant.remove') }}">
            <i class="fa-solid fa-trash-can"></i>
        </button>
    </div>

    <input type="hidden" name="variants[{{ $idx }}][id]" value="{{ $variant['id'] ?? '' }}">

    <div class="mb-4">
        <label class="block text-sm font-medium text-primary mb-2">
            {{ __('admin/product.fields.attributes') }} <span class="text-red-500">*</span>
        </label>

        <div class="flex flex-wrap gap-4">
            @foreach ($attributeGroups as $attributeName => $values)
                <div class="min-w-40 flex-1">
                    <p class="text-xs text-gray-500 mb-1">{{ $attributeName }}</p>
                    <select name="variants[{{ $idx }}][attribute_value_ids][]"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/30">
                        <option value="">{{ __('admin/product.variant.select_attributes') }}</option>
                        @foreach ($values as $valueId => $valueLabel)
                            <option value="{{ $valueId }}" @selected(in_array($valueId, $variant['attribute_value_ids'] ?? [], false))>
                                {{ $valueLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endforeach
        </div>

        @error("variants.$idx.attribute_value_ids")
            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-primary mb-2">{{ __('admin/product.fields.sku') }}</label>
            <input type="text" name="variants[{{ $idx }}][sku]" value="{{ $variant['sku'] ?? '' }}"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/30 {{ $errors->has("variants.$idx.sku") ? 'is-invalid' : 'border-gray-300' }}">
            @error("variants.$idx.sku")
                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-primary mb-2">
                {{ __('admin/product.fields.price') }} <span class="text-red-500">*</span>
            </label>
            <input type="number" name="variants[{{ $idx }}][price]" value="{{ $variant['price'] ?? '' }}" min="0"
                step="1000"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/30 {{ $errors->has("variants.$idx.price") ? 'is-invalid' : 'border-gray-300' }}">
            @error("variants.$idx.price")
                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label
                class="block text-sm font-medium text-primary mb-2">{{ __('admin/product.fields.sale_price') }}</label>
            <input type="number" name="variants[{{ $idx }}][sale_price]" value="{{ $variant['sale_price'] ?? '' }}"
                min="0" step="1000"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/30 {{ $errors->has("variants.$idx.sale_price") ? 'is-invalid' : 'border-gray-300' }}">
            @error("variants.$idx.sale_price")
                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <label class="flex items-center gap-2 mt-4 cursor-pointer">
        <input type="hidden" name="variants[{{ $idx }}][is_active]" value="0">
        <input type="checkbox" name="variants[{{ $idx }}][is_active]" value="1"
            @checked($variant['is_active'] ?? true) class="h-4 w-4 rounded accent-accent">
        <span class="text-sm text-gray-700">{{ __('admin/product.fields.is_active') }}</span>
    </label>
</div>
