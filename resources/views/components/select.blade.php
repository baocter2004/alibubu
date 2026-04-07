@props([
    'label' => '',
    'name' => '',
    'options' => [],
    'value' => '',
    'placeholder' => '',
    'icon' => '',
])

@php
    $hasError = $errors->has($name);
@endphp

<div class="w-full">
    <label for="{{ $name }}" class="flex items-center gap-x-2 text-sm font-medium text-blue-500 mb-2">
        @if ($icon)
            <i class="fa-solid fa-{{ $icon }}"></i>
        @endif
        {{ $label }}
    </label>

    <select name="{{ $name }}" id="{{ $name }}"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 {{ $hasError ? 'is-invalid' : '' }}">
        @if ($placeholder)
            <option value="" disabled {{ old($name, $value) === '' ? 'selected' : '' }}>
                {{ $placeholder }}
            </option>
        @endif
        @foreach ($options as $optValue => $optLabel)
            <option value="{{ $optValue }}"
                {{ (string) old($name, request($name, $value)) === (string) $optValue ? 'selected' : '' }}>
                {{ $optLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror
</div>
