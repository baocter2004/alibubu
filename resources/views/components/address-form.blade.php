@php
    $idx = $index ?? 'INDEX';
@endphp

<div class="address-item w-full p-6 bg-gray-50 border border-gray-200 rounded-lg relative group animate-fade-in"
    data-index="{{ $idx }}">
    @if ($idx !== 0)
        <button type="button" class="remove-address-btn absolute top-4 right-4 text-red-500 hover:text-red-700">
            <i class="fa-solid fa-trash-can"></i>
        </button>
    @endif

    <input type="hidden" name="user_addresses[{{ $idx }}][id]" value="{{ $address['id'] ?? '' }}">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        @include('components.input', [
            'name' => "user_addresses[$idx][fullname]",
            'label' => 'Họ và tên người nhận',
            'placeholder' => 'Nguyễn Văn A',
            'icon' => 'user',
            'value' => $address['fullname'] ?? '',
        ])
        @include('components.input', [
            'name' => "user_addresses[$idx][phone_number]",
            'label' => 'Số điện thoại',
            'placeholder' => '0987xxxxxx',
            'icon' => 'phone',
            'value' => $address['phone_number'] ?? '',
        ])
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        @include('components.select', [
            'name' => "user_addresses[$idx][province_id]",
            'label' => 'Tỉnh/Thành phố',
            'placeholder' => '-- Chọn tỉnh --',
            'options' => $provinces->pluck('name', 'id')->toArray(),
            'class' => 'province-select',
            'value' => $address['province_id'] ?? '',
        ])

        @php
            $oldProvince = old("user_addresses.$idx.province_id") ?? ($address['province_id'] ?? null);
            $wardOptions = $oldProvince
                ? \App\Models\Ward::where('province_id', $oldProvince)->pluck('name', 'id')->toArray()
                : [];
        @endphp

        @include('components.select', [
            'name' => "user_addresses[$idx][ward_id]",
            'label' => 'Phường/Xã',
            'placeholder' => '-- Chọn xã --',
            'options' => $wardOptions,
            'class' => 'ward-select',
            'value' => $address['ward_id'] ?? '',
            'disabled' => !$oldProvince,
        ])
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
        @include('components.input', [
            'name' => "user_addresses[$idx][address]",
            'label' => 'Địa chỉ chi tiết',
            'placeholder' => 'Số nhà, tên đường...',
            'icon' => 'location-dot',
            'value' => $address['address'] ?? '',
        ])

        @php
            $defaultCheckboxId = "is_default_{$idx}";
        @endphp

        <div class="flex items-center mb-4">
            <input type="checkbox" id="{{ $defaultCheckboxId }}" name="user_addresses[{{ $idx }}][is_default]"
                value="1"
                class="address-default-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                {{ !empty($address['is_default']) ? 'checked' : '' }}>

            <label class="ml-2 text-sm font-medium text-gray-900" for="{{ $defaultCheckboxId }}">
                Đặt làm địa chỉ mặc định
            </label>
        </div>
    </div>
</div>
