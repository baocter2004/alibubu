@extends('client.layouts.app')

@section('title', 'Alibubu - Quên mật khẩu')

@section('content')
    <div class="max-w-md mx-auto py-10">
        <div class="text-center mb-8">
            <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary/10 text-primary mb-4">
                <i class="fa-solid fa-key text-xl"></i>
            </span>
            <h1 class="text-2xl font-bold text-foreground mb-2">Quên mật khẩu?</h1>
            <p class="text-sm text-muted-foreground">
                Nhập email đã đăng ký, chúng tôi sẽ gửi liên kết đặt lại mật khẩu cho bạn.
            </p>
        </div>

        <div class="bg-card border border-border rounded-2xl shadow-sm p-6 md:p-8">
            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-foreground mb-1.5">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i
                            class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="abc@example.com" autocomplete="email"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all {{ $errors->has('email') ? 'is-invalid' : 'border-border' }}">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary/90 transition-colors">
                    <i class="fa-solid fa-paper-plane"></i>
                    Gửi liên kết đặt lại
                </button>
            </form>

            <p class="text-center text-sm text-muted-foreground mt-6">
                Nhớ mật khẩu rồi?
                <a href="{{ route('auth.client.showFormLogin') }}"
                    class="font-medium text-primary hover:underline">Đăng nhập</a>
            </p>
        </div>
    </div>
@endsection
