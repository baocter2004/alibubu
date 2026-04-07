@extends('admin.layouts.app')

@section('title')
    Trang thêm mới người dùng
@endsection

@section('content')
    <div class="max-w-5xl m-auto">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Thêm mới người dùng</h1>
            <p class="text-gray-600 mt-1">Điền đầy đủ thông tin để tạo tài khoản mới.</p>
        </div>
        <form action="{{ route('admin.users.confirm') }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- // Thông Tin Cơ Bản --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                    1
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Thông Tin Cơ Bản
                    </h2>
                    <p class="text-sm text-gray-500">
                        Nhập thông tin cá nhân của người dùng
                    </p>
                </div>
            </div>
            <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @include('components.input', [
                        'name' => 'fullname',
                        'required' => true,
                        'label' => 'Họ Và Tên',
                        'placeholder' => 'Mời nhập họ và tên',
                        'value' => $data['fullname'] ?? '',
                        'icon' => 'user-tag',
                    ])
                    @include('components.input', [
                        'name' => 'email',
                        'required' => true,
                        'label' => 'Email',
                        'placeholder' => 'Mời nhập email',
                        'value' => $data['email'] ?? '',
                        'icon' => 'envelope',
                    ])
                </div>

                @include('components.input', [
                    'name' => 'phone_number',
                    'required' => true,
                    'label' => 'Số Điện Thoại',
                    'placeholder' => 'Mời nhập số điện thoại',
                    'value' => $data['phone_number'] ?? '',
                    'icon' => 'phone',
                ])

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @include('components.input', [
                        'name' => 'password',
                        'label' => 'Mật khẩu',
                        'type' => 'password',
                        'required' => true,
                        'placeholder' => '********************',
                        'icon' => 'lock',
                    ])

                    @include('components.input', [
                        'name' => 'password_confirmation',
                        'label' => 'Xác nhận mật khẩu',
                        'type' => 'password',
                        'required' => true,
                        'placeholder' => '********************',
                        'icon' => 'lock',
                    ])
                </div>
            </div>

            {{-- // Thông Tin Cá Nhân --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                    2
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Thông Tin Cá Nhân
                    </h2>
                    <p class="text-sm text-gray-500">
                        Thông tin bổ sung
                    </p>
                </div>
            </div>
            <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @include('components.select', [
                        'name' => 'gender',
                        'label' => 'Giới tính',
                        'icon' => 'venus-mars',
                        'placeholder' => 'Chọn giới tính',
                        'value' => $data['gender'] ?? '',
                        'options' => \App\Const\UserConst::GENDER,
                    ])

                    @include('components.date', [
                        'name' => 'birthday',
                        'label' => 'Ngày sinh',
                        'placeholder' => '10/04/2004',
                        'value' => $data['birthday'] ?? '',
                    ])
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @include('components.select', [
                        'name' => 'role',
                        'label' => 'Vai trò',
                        'icon' => 'user-shield',
                        'value' => $data['role'] ?? '',
                        'options' => \App\Const\UserConst::ROLE,
                    ])

                    @include('components.select', [
                        'name' => 'status',
                        'label' => 'Trạng thái',
                        'icon' => 'toggle-on',
                        'value' => $data['status'] ?? '',
                        'options' => \App\Const\UserConst::STATUS,
                    ])
                </div>
            </div>

            {{-- // Địa chỉ Nhận Hàng --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                        3
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Địa Chỉ Nhận Hàng</h2>
                        <p class="text-sm text-gray-500">Tối đa 5 địa chỉ (<span id="address-count">0</span>/5)</p>
                    </div>
                </div>
                <button type="button" id="add-address-btn"
                    class="px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-all">
                    <i class="fa-solid fa-plus mr-1"></i> Thêm địa chỉ
                </button>
            </div>

            <div
                class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4 {{ $errors->has('user_addresses') ? 'is-invalid bg-red-100 border-red-500' : '' }}">
                @php
                    $currentAddresses = old('user_addresses') ?? ($data['user_addresses'] ?? [[]]);

                    $hasDefault = false;
                    foreach ($currentAddresses as $i => &$addr) {
                        if (!empty($addr['is_default']) && !$hasDefault) {
                            $addr['is_default'] = true;
                            $hasDefault = true;
                        } else {
                            $addr['is_default'] = false;
                        }
                    }
                @endphp

                <div id="address-container" class="space-y-4 mb-12">
                    @foreach ($currentAddresses as $index => $address)
                        @include('components.address-form', [
                            'index' => $index,
                            'provinces' => $provinces,
                        ])
                    @endforeach
                </div>

                <template id="address-template">
                    @include('components.address-form', [
                        'index' => 'INDEX',
                        'provinces' => $provinces,
                        'address' => [],
                    ])
                </template>

                <div class="text-center">
                    <i class="fa-solid fa-triangle-exclamation text-yellow-500 mr-1"></i>
                    <span class="text-sm font-medium text-gray-700">
                        Lưu ý: Nếu có nhiều hơn 1 địa chỉ, vui lòng chọn 1 địa chỉ mặc định để làm địa chỉ nhận hàng chính.
                    </span>
                </div>

                @if ($errors->has('user_addresses'))
                    <div class="mt-2 p-2 bg-red-100 border text-center border-red-500 text-red-800 rounded-lg is-invalid">
                        {{ $errors->first('user_addresses') }}
                    </div>
                @endif
            </div>

            {{-- // Thông Tin Ngân hàng --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                    4
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Thông Tin Ngân Hàng
                    </h2>
                    <p class="text-sm text-gray-500">
                        Không bắt buộc
                    </p>
                </div>
            </div>
            <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
                @include('components.input', [
                    'name' => 'bank_name',
                    'label' => 'Tên ngân hàng',
                    'icon' => 'building-columns',
                    'value' => $data['bank_name'] ?? '',
                ])

                @include('components.input', [
                    'name' => 'user_bank_name',
                    'label' => 'Tên chủ tài khoản',
                    'icon' => 'user',
                    'value' => $data['user_bank_name'] ?? '',
                ])

                @include('components.input', [
                    'name' => 'bank_account',
                    'label' => 'Số tài khoản',
                    'icon' => 'credit-card',
                    'value' => $data['bank_account'] ?? '',
                ])
            </div>

            <div class="w-full p-4 mt-4 bg-white rounded-lg shadow-lg grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-4">

                <!-- Back -->
                <a href="{{ route('admin.users.index') }}"
                    class="w-full flex justify-center items-center gap-2 p-2 md:p-4 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 hover:text-gray-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300">

                    <i class="fa-solid fa-arrow-left"></i>
                    Quay Lại Danh Sách
                </a>

                <!-- Submit -->
                <button type="submit"
                    class="w-full flex justify-center items-center gap-2 p-2 md:p-4 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <i class="fa-solid fa-plus"></i>
                    Thêm Mới Người Dùng
                </button>

            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('address-container');
            const template = document.getElementById('address-template').innerHTML;
            const addBtn = document.getElementById('add-address-btn');
            const countSpan = document.getElementById('address-count');

            function updateUI() {
                const items = container.querySelectorAll('.address-item');
                countSpan.innerText = items.length;
                addBtn.style.display = items.length >= 5 ? 'none' : 'inline-flex';
            }

            // 1. Thêm địa chỉ
            addBtn.addEventListener('click', function() {
                if (container.querySelectorAll('.address-item').length < 5) {
                    const index = Date.now(); // Tạo index duy nhất
                    const html = template.replace(/INDEX/g, index);
                    container.insertAdjacentHTML('beforeend', html);
                    updateUI();
                }
            });

            // 2. Xóa địa chỉ
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-address-btn')) {
                    e.target.closest('.address-item').remove();
                    updateUI();
                }
            });

            // 3. Load Phường/Xã khi chọn Tỉnh
            container.addEventListener('change', async function(e) {
                if (e.target.classList.contains('province-select')) {
                    const provinceId = e.target.value;
                    const wardSelect = e.target.closest('.address-item').querySelector('.ward-select');

                    console.log(provinceId)

                    wardSelect.innerHTML = '<option value="">Đang tải...</option>';
                    wardSelect.disabled = true;

                    if (provinceId) {
                        try {
                            const res = await fetch(`/api/get-wards/${provinceId}`);
                            const wards = await res.json();
                            console.log(wards)

                            let options = '<option value="">-- Chọn xã --</option>';
                            wards.forEach(w => options += `<option value="${w.id}">${w.name}</option>`);
                            wardSelect.innerHTML = options;
                            wardSelect.disabled = false;
                        } catch (err) {
                            wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                        }
                    }
                }
            });

            // 4. Chỉ cho phép chọn 1 địa chỉ mặc định
            container.addEventListener('change', function(e) {
                if (e.target.classList.contains('address-default-checkbox') && e.target.checked) {
                    container.querySelectorAll('.address-default-checkbox').forEach(cb => {
                        if (cb !== e.target) cb.checked = false;
                    });
                }
            });

            updateUI(); // Khởi tạo số lượng lúc đầu
        });
    </script>
@endpush
