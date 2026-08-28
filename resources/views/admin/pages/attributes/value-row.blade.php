@php
    $idx = $index ?? 'INDEX';
    $value = $value ?? [];
@endphp

<div class="value-item flex items-start gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3" data-index="{{ $idx }}">
    <input type="hidden" name="values[{{ $idx }}][id]" value="{{ $value['id'] ?? '' }}">

    <div class="flex-1 min-w-0">
        <input type="text" name="values[{{ $idx }}][value]" value="{{ $value['value'] ?? '' }}"
            placeholder="{{ __('admin/attribute.fields.value') }}"
            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has("values.$idx.value") ? 'is-invalid' : 'border-gray-300' }}">
        @error("values.$idx.value")
            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <label class="flex items-center gap-2 cursor-pointer pt-2 whitespace-nowrap">
        <input type="hidden" name="values[{{ $idx }}][is_active]" value="0">
        <input type="checkbox" name="values[{{ $idx }}][is_active]" value="1"
            @checked($value['is_active'] ?? true) class="h-4 w-4 rounded accent-blue-500">
        <span class="text-sm text-gray-700">{{ __('admin/attribute.fields.is_active') }}</span>
    </label>

    <button type="button" class="remove-value-btn pt-2 text-red-500 hover:text-red-700"
        title="{{ __('admin/attribute.value_section.remove') }}">
        <i class="fa-solid fa-trash-can"></i>
    </button>
</div>
