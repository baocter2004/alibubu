<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (!empty($user->id))
        @method('PUT')
    @endif
    {{-- Thông Tin Cơ Bản --}}
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
                'value' => $user->fullname ?? ($data['fullname'] ?? ''),
                'icon' => 'user-tag',
            ])
            @include('components.input', [
                'name' => 'email',
                'required' => true,
                'label' => 'Email',
                'placeholder' => 'Mời nhập email',
                'value' => $user->email ?? ($data['email'] ?? ''),
                'icon' => 'envelope',
            ])
        </div>

        @include('components.input', [
            'name' => 'phone_number',
            'required' => true,
            'label' => 'Số Điện Thoại',
            'placeholder' => 'Mời nhập số điện thoại',
            'value' => $user->phone_number ?? ($data['phone_number'] ?? ''),
            'icon' => 'phone',
        ])

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @include('components.input', [
                'name' => 'password',
                'label' => 'Mật khẩu',
                'type' => 'password',
                'required' => empty($user->id),
                'placeholder' => '********************',
                'icon' => 'lock',
            ])

            @include('components.input', [
                'name' => 'password_confirmation',
                'label' => 'Xác nhận mật khẩu',
                'type' => 'password',
                'required' => empty($user->id),
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
                'value' => $user->gender ?? ($data['gender'] ?? ''),
                'options' => \App\Const\UserConst::GENDER,
            ])

            @include('components.date', [
                'name' => 'birthday',
                'label' => 'Ngày sinh',
                'placeholder' => '10/04/2004',
                'value' => $user->birthday ?? ($data['birthday'] ?? ''),
            ])
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @include('components.select', [
                'name' => 'role',
                'label' => 'Vai trò',
                'icon' => 'user-shield',
                'value' => $user->role ?? ($data['role'] ?? ''),
                'options' => \App\Const\UserConst::ROLE,
            ])

            @include('components.select', [
                'name' => 'status',
                'label' => 'Trạng thái',
                'icon' => 'toggle-on',
                'value' => $user->status ?? ($data['status'] ?? ''),
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
            $currentAddresses = [];

            if (!empty($user)) {
                $currentAddresses = $user->userAddresses->toArray();
            } else {
                $currentAddresses = old('user_addresses') ?? ($data['user_addresses'] ?? [[]]);
            }

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
            'value' => $user->bank_name ?? ($data['bank_name'] ?? ''),
        ])

        @include('components.input', [
            'name' => 'user_bank_name',
            'label' => 'Tên chủ tài khoản',
            'icon' => 'user',
            'value' => $user->user_bank_name ?? ($data['user_bank_name'] ?? ''),
        ])

        @include('components.input', [
            'name' => 'bank_account',
            'label' => 'Số tài khoản',
            'icon' => 'credit-card',
            'value' => $user->bank_account ?? ($data['bank_account'] ?? ''),
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
            {{ empty($user->id) ? 'Tạo Người Dùng' : 'Cập Nhật Thông Tin' }}
        </button>

    </div>
</form>
