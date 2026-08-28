<header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-border shadow-sm">
    <div class="hidden md:block bg-foreground text-white text-xs py-1.5">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <span><i class="fa-solid fa-truck mr-1"></i> Miễn phí vận chuyển cho đơn từ 500.000đ</span>
            <div class="flex gap-4">
                <a href="{{ route('about') }}" class="hover:text-primary transition-colors">Về chúng tôi</a>
                <a href="{{ route('shop.index') }}" class="hover:text-primary transition-colors">Khuyến mãi</a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center gap-4 h-16">
            <div class="flex items-center gap-4">
                <button id="menu-open" type="button"
                    class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg hover:bg-muted transition-colors"
                    aria-label="Mở menu">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <a href="{{ route('index') }}" class="flex items-center gap-2 shrink-0">
                    <span
                        class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white font-bold text-sm">A</span>
                    <span class="font-bold text-lg text-foreground tracking-tight">Alibubu</span>
                </a>

                <nav class="hidden md:flex items-center gap-6 ml-4">
                    <a href="{{ route('index') }}"
                        class="text-sm font-medium transition-colors {{ request()->routeIs('index') ? 'text-primary' : 'text-muted-foreground hover:text-foreground' }}">
                        Trang chủ
                    </a>
                    <a href="{{ route('shop.index') }}"
                        class="text-sm font-medium transition-colors {{ request()->routeIs('shop.*') ? 'text-primary' : 'text-muted-foreground hover:text-foreground' }}">
                        Sản phẩm
                    </a>
                    <a href="{{ route('shop.index', ['is_sale' => 1]) }}"
                        class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                        Ưu đãi
                    </a>
                    <a href="{{ route('about') }}"
                        class="text-sm font-medium transition-colors {{ request()->routeIs('about') ? 'text-primary' : 'text-muted-foreground hover:text-foreground' }}">
                        Giới thiệu
                    </a>
                </nav>
            </div>

            <form action="{{ route('shop.index') }}" method="GET" class="hidden lg:block flex-1 max-w-sm">
                <div class="relative">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                    <input type="search" name="keyword" value="{{ request('keyword') }}"
                        placeholder="Tìm sản phẩm..."
                        class="w-full pl-9 pr-4 py-2 text-sm bg-muted border border-transparent rounded-xl focus:outline-none focus:bg-white focus:border-primary transition-all">
                </div>
            </form>

            <div class="flex items-center gap-1">
                <a href="{{ route('cart.index') }}"
                    class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-muted transition-colors relative"
                    aria-label="Giỏ hàng">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                    @if ($cartCount > 0)
                        <span
                            class="absolute -top-0.5 -right-0.5 bg-primary text-white text-[10px] font-bold min-w-4.5 h-4.5 rounded-full flex items-center justify-center px-1 border-2 border-white">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                    @endif
                </a>

                @auth
                    <div class="hidden md:flex items-center gap-1">
                        <span class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-foreground">
                            <i class="fa-regular fa-user"></i>
                            {{ Auth::user()->fullname }}
                        </span>
                        <form action="{{ route('auth.client.logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                @else
                    <div class="hidden sm:flex items-center gap-2 ml-2">
                        <a href="{{ route('auth.client.showFormLogin') }}"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-foreground hover:bg-muted transition-colors">
                            <i class="fa-regular fa-user"></i>
                            Đăng nhập
                        </a>
                        <a href="{{ route('auth.client.showFormRegister') }}"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary hover:bg-primary/90 shadow-sm transition-colors">
                            <i class="fa-solid fa-user-plus"></i>
                            Đăng ký
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>
