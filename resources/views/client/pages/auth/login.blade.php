@extends('client.layouts.app')

@section('title', 'Alibubu - Đăng Nhập Tài Khoản')

@section('content')
    <div class="w-full mt-10 m-auto">
        <div class="flex flex-col justify-center items-center gap-4">
            <h1 class="font-bold ">
                Welcome Back
            </h1>
            <h2>
                Enter your details to access your curated collection
            </h2>
        </div>
        <div class="w-full m-auto max-w-xl bg-white rounded-lg shadow-md mt-10 p-6 md:p-10">
            <form action="{{ route('auth.client.handleLogin') }}" method="POST" class="flex flex-col justify-center items-center space-y-4">
                @csrf

                @include('components.title', [
                    'text' => 'Login'
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

                @include('components.button', [
                    'type' => 'submit',
                    'color' => 'blue',
                    'text' => 'Login'
                ])
            </form>
        </div>
    @endsection
