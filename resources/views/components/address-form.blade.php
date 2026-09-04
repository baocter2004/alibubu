@php
    $idx = $index ?? 'INDEX';
    $address = $address ?? [];
    $oldProvince = old("user_addresses.$idx.province_id") ?? ($address['province_id'] ?? null);
    $wardOptions = $oldProvince
        ? \App\Models\Ward::where('province_id', $oldProvince)->orderBy('name')->pluck('name', 'id')->toArray()
        : [];
@endphp

<div class="address-item relative bg-gray-50 border border-border rounded-xl p-5" data-index="{{ $idx }}">
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm font-semibold text-gray-700">
            <i class="fa-solid fa-location-dot text-accent mr-1.5"></i>
            {{ __('admin/user.address.title') }}<span class="address-title">{{ is_numeric($idx) ? $idx + 1 : 1 }}</span>
        </p>

        <button type="button" class="remove-address-btn text-red-500 hover:text-red-700"
            title="{{ __('admin/user.address.remove') }}">
            <i class="fa-solid fa-trash-can"></i>
        </button>
    </div>

    <input type="hidden" name="user_addresses[{{ $idx }}][id]" value="{{ $address['id'] ?? '' }}">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        @include('components.input', [
            'name' => "user_addresses[$idx][fullname]",
            'label' => __('admin/user.address.recipient'),
            'icon' => 'user',
            'value' => $address['fullname'] ?? '',
        ])

        @include('components.input', [
            'name' => "user_addresses[$idx][phone_number]",
            'label' => __('admin/user.address.phone_number'),
            'icon' => 'phone',
            'value' => $address['phone_number'] ?? '',
        ])
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        @include('components.select', [
            'name' => "user_addresses[$idx][province_id]",
            'label' => __('admin/user.address.province'),
            'placeholder' => __('admin/user.address.select_province'),
            'options' => $provinces->pluck('name', 'id')->toArray(),
            'class' => 'province-select',
            'value' => $address['province_id'] ?? '',
        ])

        @include('components.select', [
            'name' => "user_addresses[$idx][ward_id]",
            'label' => __('admin/user.address.ward'),
            'placeholder' => __('admin/user.address.select_ward'),
            'options' => $wardOptions,
            'class' => 'ward-select',
            'value' => $address['ward_id'] ?? '',
        ])
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
        @include('components.input', [
            'name' => "user_addresses[$idx][address]",
            'label' => __('admin/user.address.detail'),
            'icon' => 'location-dot',
            'value' => $address['address'] ?? '',
        ])

        <label class="flex items-center gap-2 cursor-pointer pb-2">
            <input type="checkbox" id="is_default_{{ $idx }}" name="user_addresses[{{ $idx }}][is_default]"
                value="1" @checked(! empty($address['is_default']))
                class="address-default-checkbox w-4 h-4 rounded accent-accent">
            <span class="text-sm text-gray-700">{{ __('admin/user.address.is_default') }}</span>
        </label>
    </div>
</div>
