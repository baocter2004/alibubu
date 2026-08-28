@extends('client.layouts.app')

@section('title', __('common.app_name') . ' - ' . __('client_auth.reset.title'))

@section('content')
    <div class="max-w-md mx-auto py-10">
        <div class="text-center mb-8">
            <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary/10 text-primary mb-4">
                <i class="fa-solid fa-lock-open text-xl"></i>
            </span>
            <h1 class="text-2xl font-bold text-foreground mb-2">{{ __('client_auth.reset.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('client_auth.reset.subheading') }}</p>
        </div>

        <div class="bg-card border border-border rounded-2xl shadow-sm p-6 md:p-8">
            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-medium text-foreground mb-1.5">
                        {{ __('client_auth.login.email') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $email) }}"
                        autocomplete="email"
                        class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('email') ? 'is-invalid' : 'border-border' }}">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-foreground mb-1.5">
                        {{ __('client_auth.reset.password') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" autocomplete="new-password"
                        class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('password') ? 'is-invalid' : 'border-border' }}">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-foreground mb-1.5">
                        {{ __('client_auth.reset.password_confirmation') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        autocomplete="new-password"
                        class="w-full px-4 py-2.5 text-sm border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                </div>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary/90 transition-colors">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ __('client_auth.reset.submit') }}
                </button>
            </form>
        </div>
    </div>
@endsection
