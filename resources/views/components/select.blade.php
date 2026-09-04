@props([
    'label' => '',
    'name' => '',
    'options' => [],
    'value' => '',
    'placeholder' => '',
    'icon' => '',
    'required' => false,
])

@php
    $dotName = str_replace(['[', ']'], ['.', ''], $name);
    $dotName = str_replace('..', '.', $dotName);

    $hasError = $errors->has($dotName);
@endphp

<div class="w-full">
    <label for="{{ $name }}" class="flex items-center gap-x-2 text-sm font-semibold text-primary mb-2">
        @if ($icon)
            <i class="fa-solid fa-{{ $icon }}"></i>
        @endif
        {{ $label }}
        @if ($required)
            <span class="text-red-500 text-base leading-none">*</span>
        @endif
    </label>

    <select name="{{ $name }}" id="{{ $name }}"
        class="w-full px-4 py-2 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-accent/30 transition-all {{ $class ?? '' }} {{ $hasError ? 'is-invalid' : 'border-border' }}">
        @if ($placeholder)
            <option value="" disabled {{ old($dotName, $value) === '' ? 'selected' : '' }}>
                {{ $placeholder }}
            </option>
        @endif

        @foreach ($options as $optValue => $optLabel)
            <option value="{{ $optValue }}"
                {{ (string) old($dotName, $value) === (string) $optValue ? 'selected' : '' }}>
                {{ $optLabel }}
            </option>
        @endforeach
    </select>

    @error($dotName)
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror
</div>
