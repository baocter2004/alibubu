<div id="mobile-menu" class="fixed inset-0 z-50 hidden">
    <div id="menu-backdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <div id="menu-panel"
        class="absolute top-0 left-0 bottom-0 w-75 bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300">

        <div class="flex items-center justify-between px-5 py-4 border-b border-border">
            <div class="flex items-center gap-2">
                <span
                    class="w-7 h-7 rounded-lg bg-primary flex items-center justify-center text-white font-bold text-sm">A</span>
                <span class="font-bold text-foreground">Alibubu</span>
            </div>
            <button id="menu-close" type="button"
                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-muted transition-colors"
                aria-label="Đóng menu">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <div class="px-5 py-4 border-b border-border">
            <form action="{{ route('shop.index') }}" method="GET" class="relative">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                <input type="search" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm kiếm..."
                    class="w-full pl-9 pr-4 py-2.5 text-sm bg-muted border border-transparent rounded-xl focus:outline-none focus:bg-white focus:border-primary transition-all">
            </form>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-3">
            <p class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">Menu</p>

            @foreach ([['index', [], 'fa-house', 'Trang chủ'], ['shop.index', [], 'fa-display', 'Sản phẩm'], ['shop.index', ['is_sale' => 1], 'fa-star', 'Ưu đãi'], ['about', [], 'fa-circle-info', 'Giới thiệu']] as [$name, $params, $icon, $label])
                <a href="{{ route($name, $params) }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium mb-1 transition-colors {{ request()->routeIs($name) && ! $params ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:text-foreground hover:bg-muted' }}">
                    <i class="fa-solid {{ $icon }} w-4 text-center"></i>
                    {{ $label }}
                </a>
            @endforeach

            <div class="border-t border-border my-4"></div>
            <p class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">Tài khoản</p>

            <a href="{{ route('cart.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors mb-1">
                <i class="fa-solid fa-bag-shopping w-4 text-center"></i>
                Giỏ hàng
                @if ($cartCount > 0)
                    <span
                        class="ml-auto bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $cartCount }}</span>
                @endif
            </a>

            @auth
                <span class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-foreground font-medium">
                    <i class="fa-regular fa-user w-4 text-center"></i>
                    {{ Auth::user()->fullname }}
                </span>
                <form action="{{ route('auth.client.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                        Đăng xuất
                    </button>
                </form>
            @else
                <a href="{{ route('auth.client.showFormLogin') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors mb-1">
                    <i class="fa-regular fa-user w-4 text-center"></i>
                    Đăng nhập
                </a>
                <a href="{{ route('auth.client.showFormRegister') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                    <i class="fa-solid fa-user-plus w-4 text-center"></i>
                    Đăng ký
                </a>
            @endauth
        </nav>
    </div>
</div>
