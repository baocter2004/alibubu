@extends('admin.layouts.app')

@section('title')
    Trang thêm mới người dùng
@endsection

@section('content')
    <form action="{{ route('admin.users.confirm') }}" class="max-w-4xl m-auto" method="POST" enctype="multipart/form-data">
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
                    'icon' => 'user-tag',
                ])
                @include('components.input', [
                    'name' => 'email',
                    'required' => true,
                    'label' => 'Email',
                    'placeholder' => 'Mời nhập email',
                    'icon' => 'envelope',
                ])
            </div>

            @include('components.input', [
                'name' => 'phone_number',
                'required' => true,
                'label' => 'Số Điện Thoại',
                'placeholder' => 'Mời nhập số điện thoại',
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
                    'required' => true,
                    'icon' => 'venus-mars',
                    'options' => \App\Const\UserConst::GENDER,
                ])

                @include('components.date', [
                    'name' => 'birthday',
                    'label' => 'Ngày sinh',
                    'required' => true,
                ])
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @include('components.select', [
                    'name' => 'role',
                    'label' => 'Vai trò',
                    'required' => true,
                    'icon' => 'user-shield',
                    'options' => \App\Const\UserConst::ROLE,
                ])

                @include('components.select', [
                    'name' => 'status',
                    'label' => 'Trạng thái',
                    'required' => true,
                    'icon' => 'toggle-on',
                    'options' => \App\Const\UserConst::STATUS,
                ])
            </div>
        </div>

        {{-- // Địa chỉ Nhận Hàng --}}
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                3
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Địa Chỉ Nhận Hàng
                </h2>
                <p class="text-sm text-gray-500">
                    Thông tin địa chỉ nhận hàng - Tối đa 5
                </p>
            </div>
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
            ])

            @include('components.input', [
                'name' => 'user_bank_name',
                'label' => 'Tên chủ tài khoản',
                'icon' => 'user',
            ])

            @include('components.input', [
                'name' => 'bank_account',
                'label' => 'Số tài khoản',
                'icon' => 'credit-card',
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
@endsection
