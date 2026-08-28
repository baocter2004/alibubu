@extends('admin.layouts.app-blank')

@section('title', 'Alibubu Admin - Đặt Lại Mật Khẩu')

@section('content')
    <div class="w-full mt-10 m-auto">
        <div class="flex flex-col justify-center items-center gap-4 text-center">
            <h1 class="text-2xl md:text-3xl text-blue-500 font-bold">Đặt lại mật khẩu</h1>
            <h2 class="text-sm md:text-base text-gray-500 leading-relaxed">
                Nhập mật khẩu mới cho tài khoản quản trị.
            </h2>
        </div>

        <div class="w-full m-auto max-w-md bg-white shadow-md rounded-md mt-10 p-6 md:p-10">
            <form action="{{ route('admin.password.update') }}" method="POST"
                class="flex flex-col justify-center items-center space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                @include('components.input', [
                    'label' => 'Email',
                    'name' => 'email',
                    'value' => $email,
                    'required' => true,
                    'icon' => 'envelope',
                ])

                @include('components.input', [
                    'label' => 'Mật khẩu mới',
                    'name' => 'password',
                    'type' => 'password',
                    'required' => true,
                    'icon' => 'lock',
                ])

                @include('components.input', [
                    'label' => 'Xác nhận mật khẩu',
                    'name' => 'password_confirmation',
                    'type' => 'password',
                    'required' => true,
                    'icon' => 'lock',
                ])

                @include('components.button', [
                    'type' => 'submit',
                    'color' => 'blue',
                    'text' => 'Đổi mật khẩu',
                ])
            </form>
        </div>
    </div>
@endsection
