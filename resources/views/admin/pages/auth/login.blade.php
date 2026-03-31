@extends('admin.layouts.app-blank')

@section('title', 'Alibubu Admin - Đăng Nhập Tài Khoản Quản Trị Viên')

@section('content')
    <div class="w-full mt-10 m-auto">
        <div class="flex flex-col justify-center items-center gap-4 text-center">
            <h1 class="text-2xl md:text-3xl font-bold">
                Welcome Back
            </h1>
            <h2 class="text-sm md:text-base text-gray-500 leading-relaxed">
                Enter your details to access your curated collection
            </h2>
        </div>

        <div class="w-full m-auto max-w-md bg-white shadow-md rounded-md mt-10 p-6 md:p-10">
            <form action="{{ route('auth.admin.handleLogin') }}" method="POST"
                class="flex flex-col justify-center items-center space-y-4">
                @csrf

                @include('components.title', [
                    'text' => 'Login',
                ])

                @include('components.input', [
                    'label' => 'Email',
                    'name' => 'email',
                    'required' => true,
                    'icon' => 'envelope',
                    'placeholder' => 'abcexample@gmail.com',
                ])

                @include('components.input', [
                    'label' => 'Password',
                    'name' => 'password',
                    'type' => 'password',
                    'required' => true,
                    'icon' => 'lock',
                    'placeholder' => '********************',
                ])

                @include('components.checkbox', [
                    'name' => 'remember',
                    'label' => 'Remember Me',
                ])

                @include('components.button', [
                    'type' => 'submit',
                    'color' => 'blue',
                    'text' => 'Login',
                ])
            </form>
        </div>
    </div>
@endsection
