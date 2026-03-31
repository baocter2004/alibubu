@extends('client.layouts.app')

@section('title', 'Alibubu - Đăng Ký Tài Khoản')

@section('content')
    <div class="w-full mt-10 m-auto">
        <div class="flex flex-col justify-center items-center gap-4 text-center">
            <h1 class="text-2xl md:text-3xl font-bold">
                Create an Account
            </h1>
            <h2 class="text-sm md:text-base text-gray-500 leading-relaxed">
                Sign up to start exploring and build your personalized experience
            </h2>
        </div>
        <div class="w-full m-auto max-w-md bg-white rounded-lg shadow-md mt-10 p-6 md:p-10">
            <form action="{{ route('auth.client.handleRegister') }}" method="POST"
                class="flex flex-col justify-center items-center space-y-4">
                @csrf

                @include('components.title', [
                    'text' => 'Register',
                ])

                @include('components.input', [
                    'label' => 'Full Name',
                    'name' => 'fullname',
                    'required' => true,
                    'icon' => 'user',
                    'placeholder' => 'John Doe',
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

                @include('components.input', [
                    'label' => 'Confirm Password',
                    'name' => 'password_confirmation',
                    'type' => 'password',
                    'required' => true,
                    'icon' => 'lock',
                    'placeholder' => '********************',
                ])

                <div class="flex justify-end w-full">
                    <a href="{{ route('auth.client.showFormLogin') }}" class="text-sm text-blue-500 hover:underline">
                        Already have an account? Login
                    </a>
                </div>

                @include('components.button', [
                    'type' => 'submit',
                    'color' => 'blue',
                    'text' => 'Login',
                ])
            </form>
        </div>
    </div>
@endsection
