@extends('admin.layouts.app-blank')

@section('title', __('common.app_name') . ' - ' . __('admin/auth.reset.title'))

@section('content')
    <div class="w-full max-w-md mx-auto py-10">
        <div class="text-center mb-8">
            <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary-soft text-primary mb-4">
                <i class="fa-solid fa-lock-open text-xl"></i>
            </span>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">{{ __('admin/auth.reset.heading') }}</h1>
            <p class="text-sm text-gray-500">{{ __('admin/auth.reset.subheading') }}</p>
        </div>

        <div class="bg-white shadow-md rounded-2xl p-6 md:p-8">
            <form action="{{ route('admin.password.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                @include('components.input', [
                    'label' => __('admin/auth.login.email'),
                    'name' => 'email',
                    'value' => $email,
                    'required' => true,
                    'icon' => 'envelope',
                ])

                @include('components.input', [
                    'label' => __('admin/auth.reset.password'),
                    'name' => 'password',
                    'type' => 'password',
                    'required' => true,
                    'icon' => 'lock',
                ])

                @include('components.input', [
                    'label' => __('admin/auth.reset.password_confirmation'),
                    'name' => 'password_confirmation',
                    'type' => 'password',
                    'required' => true,
                    'icon' => 'lock',
                ])

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary-hover transition-colors">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ __('admin/auth.reset.submit') }}
                </button>
            </form>
        </div>
    </div>
@endsection
