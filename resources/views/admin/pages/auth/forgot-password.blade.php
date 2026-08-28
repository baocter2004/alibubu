@extends('admin.layouts.app-blank')

@section('title', __('common.app_name') . ' - ' . __('admin/auth.forgot.title'))

@section('content')
    <div class="w-full max-w-md mx-auto py-10">
        <div class="text-center mb-8">
            <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 mb-4">
                <i class="fa-solid fa-key text-xl"></i>
            </span>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">{{ __('admin/auth.forgot.heading') }}</h1>
            <p class="text-sm text-gray-500">{{ __('admin/auth.forgot.subheading') }}</p>
        </div>

        <div class="bg-white shadow-md rounded-2xl p-6 md:p-8">
            <form action="{{ route('admin.password.email') }}" method="POST" class="space-y-5">
                @csrf

                @include('components.input', [
                    'label' => __('admin/auth.login.email'),
                    'name' => 'email',
                    'required' => true,
                    'icon' => 'envelope',
                    'placeholder' => 'admin@example.com',
                ])

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-blue-500 rounded-xl hover:bg-blue-600 transition-colors">
                    <i class="fa-solid fa-paper-plane"></i>
                    {{ __('admin/auth.forgot.submit') }}
                </button>
            </form>

            <p class="text-center mt-6">
                <a href="{{ route('auth.admin.showFormLogin') }}" class="text-sm text-blue-500 hover:underline">
                    {{ __('admin/auth.forgot.back') }}
                </a>
            </p>
        </div>
    </div>
@endsection
