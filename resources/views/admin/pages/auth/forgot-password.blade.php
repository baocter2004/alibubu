@extends('admin.layouts.app-blank')

@section('title', 'Alibubu Admin - Quên Mật Khẩu')

@section('content')
    <div class="w-full mt-10 m-auto">
        <div class="flex flex-col justify-center items-center gap-4 text-center">
            <h1 class="text-2xl md:text-3xl text-blue-500 font-bold">Quên mật khẩu</h1>
            <h2 class="text-sm md:text-base text-gray-500 leading-relaxed max-w-md">
                Nhập email quản trị của bạn, hệ thống sẽ gửi liên kết đặt lại mật khẩu.
            </h2>
        </div>

        <div class="w-full m-auto max-w-md bg-white shadow-md rounded-md mt-10 p-6 md:p-10">
            <form action="{{ route('admin.password.email') }}" method="POST"
                class="flex flex-col justify-center items-center space-y-4">
                @csrf

                @include('components.input', [
                    'label' => 'Email',
                    'name' => 'email',
                    'required' => true,
                    'icon' => 'envelope',
                    'placeholder' => 'admin@example.com',
                ])

                @include('components.button', [
                    'type' => 'submit',
                    'color' => 'blue',
                    'text' => 'Gửi liên kết đặt lại',
                ])

                <a href="{{ route('auth.admin.showFormLogin') }}" class="text-sm text-blue-500 hover:underline">
                    Quay lại đăng nhập
                </a>
            </form>
        </div>
    </div>
@endsection
