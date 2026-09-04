@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client_auth.register.title'))

@section('content')
    <div class="max-w-md mx-auto py-10">
        <div class="text-center mb-8">
            <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary/10 text-primary mb-4">
                <i class="fa-solid fa-user-plus text-xl"></i>
            </span>
            <h1 class="text-2xl font-bold text-foreground mb-2">{{ __('client_auth.register.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('client_auth.register.subheading') }}</p>
        </div>

        <div class="bg-card border border-border rounded-2xl shadow-sm p-6 md:p-8">
            <form action="{{ route('auth.client.handleRegister') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="fullname" class="block text-sm font-medium text-foreground mb-1.5">
                        {{ __('client_auth.register.fullname') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i
                            class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                        <input type="text" id="fullname" name="fullname" value="{{ old('fullname') }}"
                            autocomplete="name"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('fullname') ? 'is-invalid' : 'border-border' }}">
                    </div>
                    @error('fullname')
                        <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-foreground mb-1.5">
                        {{ __('client_auth.register.email') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i
                            class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                        <input type="email" id="email" name="email" value="{{ old('email', request('email')) }}"
                            autocomplete="email"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('email') ? 'is-invalid' : 'border-border' }}">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-foreground mb-1.5">
                        {{ __('client_auth.register.password') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i
                            class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                        <input type="password" id="password" name="password" autocomplete="new-password"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('password') ? 'is-invalid' : 'border-border' }}">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-foreground mb-1.5">
                        {{ __('client_auth.register.password_confirmation') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i
                            class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            autocomplete="new-password"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    </div>
                </div>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold btn-primary rounded-xl">
                    <i class="fa-solid fa-user-plus"></i>
                    {{ __('client_auth.register.submit') }}
                </button>
            </form>

            <p class="text-center text-sm text-muted-foreground mt-6">
                {{ __('client_auth.register.have_account') }}
                <a href="{{ route('auth.client.showFormLogin') }}" class="font-medium text-primary hover:underline">
                    {{ __('client_auth.register.login_link') }}
                </a>
            </p>
        </div>
    </div>
@endsection
