@props([
    'name' => '',
    'label' => '',
])

<div class="flex items-center w-full">
    <input type="checkbox" id="{{ $name }}" name="{{ $name }}"
        class="h-4 w-4 border-gray-300 rounded accent-blue-500 focus:ring-blue-500">
    <label for="{{ $name }}" class="ml-2 text-sm text-gray-700">
        {{ $label }}
    </label>
</div>
