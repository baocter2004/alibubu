@extends('client.layouts.app')

@section('title', 'Alibubu - Mua sắm thông minh')

@section('content')

<section class="rounded-2xl overflow-hidden bg-gradient-to-br from-orange-50 via-white to-orange-50 border border-border mb-10">
    <div class="flex flex-col md:flex-row items-center gap-8 px-6 py-10 md:px-12 md:py-14">
        <div class="flex-1 text-center md:text-left">
            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-primary/10 text-primary mb-4">
                <i class="fa-solid fa-fire mr-1"></i> Flash Sale hôm nay
            </span>
            <h1 class="text-3xl md:text-5xl font-bold text-foreground leading-tight mb-4">
                Mua sắm thông minh<br>
                <span class="text-primary">Giá tốt mỗi ngày</span>
            </h1>
            <p class="text-muted-foreground mb-7 max-w-md mx-auto md:mx-0">
                Hàng ngàn sản phẩm chính hãng, giao hàng nhanh toàn quốc, đổi trả dễ dàng trong 30 ngày.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                <a href="/shop" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary/90 transition-colors">
                    Mua ngay
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="#" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-foreground font-semibold rounded-xl border border-border hover:bg-muted transition-colors">
                    Xem ưu đãi
                </a>
            </div>
        </div>
        <div class="flex-1 flex justify-center">
            <div class="w-64 h-64 md:w-80 md:h-80 bg-white rounded-2xl shadow-lg flex items-center justify-center border border-border">
                <i class="fa-solid fa-bag-shopping text-8xl text-primary/20"></i>
            </div>
        </div>
    </div>
</section>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
    @foreach([
        ['fa-solid fa-store', '10.000+', 'Sản phẩm'],
        ['fa-solid fa-users', '50.000+', 'Khách hàng'],
        ['fa-solid fa-star', '4.9/5', 'Đánh giá'],
        ['fa-solid fa-truck', '24h', 'Giao hàng'],
    ] as $stat)
    <div class="bg-card border border-border rounded-xl p-4 text-center">
        <div class="text-2xl mb-1 text-primary"><i class="{{ $stat[0] }}"></i></div>
        <div class="text-xl font-bold text-foreground">{{ $stat[1] }}</div>
        <div class="text-xs text-muted-foreground">{{ $stat[2] }}</div>
    </div>
    @endforeach
</div>

<section class="mb-12">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-foreground">Danh mục nổi bật</h2>
            <p class="text-sm text-muted-foreground mt-0.5">Khám phá theo danh mục yêu thích</p>
        </div>
        <a href="#" class="text-sm font-medium text-primary hover:underline">Xem tất cả</a>
    </div>
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
        @foreach([
            ['fa-solid fa-shirt', 'Thời trang', '#'],
            ['fa-solid fa-mobile-screen', 'Điện tử', '#'],
            ['fa-solid fa-house', 'Nhà cửa', '#'],
            ['fa-solid fa-wand-sparkles', 'Làm đẹp', '#'],
            ['fa-solid fa-book', 'Sách', '#'],
            ['fa-solid fa-gamepad', 'Gaming', '#'],
        ] as $cat)
        <a href="{{ $cat[2] }}" class="flex flex-col items-center gap-2 p-3 bg-card border border-border rounded-xl hover:border-primary hover:bg-primary/5 transition-all group">
            <i class="{{ $cat[0] }} text-2xl text-muted-foreground group-hover:text-primary group-hover:scale-110 transition-all"></i>
            <span class="text-xs font-medium text-foreground text-center">{{ $cat[1] }}</span>
        </a>
        @endforeach
    </div>
</section>

<section class="mb-12">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-foreground">Sản phẩm nổi bật</h2>
            <p class="text-sm text-muted-foreground mt-0.5">Được yêu thích nhất tuần này</p>
        </div>
        <a href="/shop" class="text-sm font-medium text-primary hover:underline">Xem tất cả</a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach([
            ['Áo thun basic unisex', '199.000đ', '299.000đ', '33', '4.8', '120'],
            ['Quần jean slim fit', '450.000đ', '650.000đ', '31', '4.7', '89'],
            ['Giày sneaker trắng', '599.000đ', '850.000đ', '30', '4.9', '234'],
            ['Túi tote vải canvas', '250.000đ', '350.000đ', '29', '4.6', '67'],
        ] as $p)
        <div class="bg-card border border-border rounded-xl overflow-hidden group hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="relative aspect-square bg-muted overflow-hidden">
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fa-solid fa-bag-shopping text-5xl text-muted-foreground/30"></i>
                </div>
                <span class="absolute top-2 left-2 px-2 py-0.5 text-[11px] font-bold bg-red-500 text-white rounded-full">-{{ $p[3] }}%</span>
                <button class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow opacity-0 group-hover:opacity-100 transition-opacity hover:text-primary">
                    <i class="fa-regular fa-heart"></i>
                </button>
            </div>
            <div class="p-3">
                <p class="text-sm font-medium text-foreground line-clamp-2 mb-2">{{ $p[0] }}</p>
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="font-bold text-primary">{{ $p[1] }}</span>
                    <span class="text-xs text-muted-foreground line-through">{{ $p[2] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1">
                        <i class="fa-solid fa-star text-xs text-yellow-400"></i>
                        <span class="text-xs text-muted-foreground">{{ $p[4] }} ({{ $p[5] }})</span>
                    </div>
                    <button class="w-7 h-7 bg-primary rounded-lg flex items-center justify-center hover:bg-primary/80 transition-colors">
                        <i class="fa-solid fa-plus text-xs text-white"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

<section class="mb-12">
    <div class="rounded-2xl bg-primary p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-6 text-white">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Đăng ký nhận ưu đãi</h2>
            <p class="text-white/70">Nhận ngay voucher 50.000đ cho đơn hàng đầu tiên.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <input type="email" placeholder="Email của bạn" class="flex-1 md:w-64 px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder:text-white/50 focus:outline-none focus:border-white transition-colors text-sm"/>
            <button class="px-6 py-3 bg-white text-primary font-semibold rounded-xl hover:bg-white/90 transition-colors whitespace-nowrap">
                Đăng ký ngay
            </button>
        </div>
    </div>
</section>

@endsection
