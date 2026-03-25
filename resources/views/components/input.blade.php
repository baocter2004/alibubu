@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'icon' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
])

@php
    $hasError = $errors->has($name);
@endphp

<div class="w-full">
    <label for="{{ $name }}" class="flex items-center gap-x-2 text-sm font-medium text-teal-500 mb-1">
        @if ($icon)
            <i class="fa-solid fa-{{ $icon }}"></i>
        @endif
        {{ $label }}
        @if ($required)
            <span class="text-red-500 text-base leading-none">*</span>
        @endif
    </label>

    <div class="relative">
        @if ($type !== 'file')
            <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
                @if ($disabled) disabled @endif value="{{ old($name, request($name, $value)) }}"
                placeholder="{{ $placeholder }}"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-300
                    {{ $hasError ? 'is-invalid' : '' }}
                    {{ $type === 'password' ? 'pr-10' : 'pr-4' }}">
        @endif

        @if ($type === 'password')
            <button type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-500 toggle-password"
                data-target="#{{ $name }}" id="toggle-{{ $name }}">
                <i class="fa-solid fa-eye"></i>
            </button>
        @endif
    </div>

    @if ($type === 'file')
        <div class="mt-2">
            <label for="{{ $name }}"
                class="flex flex-col items-center justify-center w-full h-60 border-2 border-dashed rounded-lg cursor-pointer overflow-hidden relative hover:bg-gray-50">

                <div id="placeholder-{{ $name }}"
                    class="flex flex-col items-center justify-center text-center p-5 {{ $value ? 'hidden' : '' }}">
                    <i class="fa-solid fa-cloud-arrow-up text-2xl text-teal-500 mb-2"></i>
                    <p class="text-sm text-gray-500">Click để chọn ảnh hoặc kéo thả vào đây</p>
                </div>

                <img id="preview-{{ $name }}" src="{{ $value ? asset('storage/' . $value) : '' }}"
                    alt="Preview"
                    class="absolute inset-0 w-full h-full object-contain {{ $value ? '' : 'hidden' }}" />

                <input id="{{ $name }}" name="{{ $name }}" type="file" class="hidden"
                    accept="image/*">
            </label>
        </div>
    @endif

    @error($name)
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror
</div>

@if ($type === 'password')
    @push('scripts')
        <script>
            $(function() {
                $('#toggle-{{ $name }}').off('click').on('click', function() {
                    let $inp = $($(this).data('target'));
                    let $icon = $(this).find('i');
                    if ($inp.attr('type') === 'password') {
                        $inp.attr('type', 'text');
                        $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    } else {
                        $inp.attr('type', 'password');
                        $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    }
                });
            });
        </script>
    @endpush
@endif

@if ($type === 'file')
    @push('scripts')
        <script>
            $(function() {
                $('#{{ $name }}').on('change', function(e) {
                    let file = e.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            $('#preview-{{ $name }}')
                                .attr('src', e.target.result)
                                .removeClass('hidden');
                            $('#placeholder-{{ $name }}').addClass('hidden');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $('#preview-{{ $name }}').addClass('hidden').attr('src', '');
                        $('#placeholder-{{ $name }}').removeClass('hidden');
                    }
                });
            });
        </script>
    @endpush
@endif
