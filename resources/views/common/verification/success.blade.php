@extends('client.layouts.app')

@section('title', 'Alibubu - Xác minh email thành công')

@section('content')
    <div class="max-w-lg mx-auto text-center py-14">
        <span class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 text-green-600 mb-6">
            <i class="fa-solid fa-envelope-circle-check text-3xl"></i>
        </span>

        <h1 class="text-2xl md:text-3xl font-bold text-foreground mb-3">Xác minh email thành công!</h1>
        <p class="text-muted-foreground mb-8">
            Tài khoản của bạn đã được kích hoạt đầy đủ. Bắt đầu mua sắm cùng Alibubu ngay hôm nay.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('shop.index') }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary/90 transition-colors">
                <i class="fa-solid fa-bag-shopping"></i> Mua sắm ngay
            </a>
            <a href="{{ route('index') }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-foreground bg-white border border-border rounded-xl hover:bg-muted transition-colors">
                <i class="fa-solid fa-house"></i> Về trang chủ
            </a>
        </div>
    </div>
@endsection
