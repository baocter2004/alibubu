@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client_auth.login.title'))

@section('content')
    <div class="max-w-md mx-auto py-10">
        <div class="text-center mb-8">
            <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary/10 text-primary mb-4">
                <i class="fa-regular fa-user text-xl"></i>
            </span>
            <h1 class="text-2xl font-bold text-foreground mb-2">{{ __('client_auth.login.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('client_auth.login.subheading') }}</p>
        </div>

        <div class="bg-card border border-border rounded-2xl shadow-sm p-6 md:p-8">
            <form action="{{ route('auth.client.handleLogin') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-foreground mb-1.5">
                        {{ __('client_auth.login.email') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i
                            class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            autocomplete="email"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('email') ? 'is-invalid' : 'border-border' }}">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-foreground mb-1.5">
                        {{ __('client_auth.login.password') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i
                            class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                        <input type="password" id="password" name="password" autocomplete="current-password"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('password') ? 'is-invalid' : 'border-border' }}">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))
                            class="h-4 w-4 rounded accent-primary">
                        <span class="text-sm text-muted-foreground">{{ __('client_auth.login.remember') }}</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary hover:underline">
                        {{ __('client_auth.login.forgot') }}
                    </a>
                </div>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold btn-primary rounded-xl">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    {{ __('client_auth.login.submit') }}
                </button>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><span class="w-full border-t border-border"></span></div>
                <div class="relative flex justify-center">
                    <span class="bg-card px-3 text-xs text-muted-foreground uppercase tracking-wider">or</span>
                </div>
            </div>

            <a href="{{ route('auth.client.redirectToGoogle') }}"
                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-foreground bg-white border border-border rounded-xl hover:bg-muted transition-colors">
                <i class="fa-brands fa-google text-red-500"></i>
                {{ __('client_auth.login.google') }}
            </a>

            <p class="text-center text-sm text-muted-foreground mt-6">
                {{ __('client_auth.login.no_account') }}
                <a href="{{ route('auth.client.showFormRegister') }}" class="font-medium text-primary hover:underline">
                    {{ __('client_auth.login.register_link') }}
                </a>
            </p>
        </div>
    </div>
@endsection
