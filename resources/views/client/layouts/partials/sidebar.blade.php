<div id="mobile-menu" class="fixed inset-0 z-50 hidden">
    <div id="menu-backdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <div id="menu-panel"
        class="absolute top-0 left-0 bottom-0 w-75 bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300">

        <div class="flex items-center justify-between px-5 py-4 border-b border-border">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-primary flex items-center justify-center text-white font-bold text-sm">
                    A
                </div>
                <span class="font-bold text-foreground">Alibubu</span>
            </div>
            <button id="menu-close"
                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-muted transition-colors">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <div class="px-5 py-4 border-b border-border">
            <div class="relative">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                <input type="text" placeholder="Tìm kiếm..."
                    class="w-full pl-9 pr-4 py-2.5 text-sm bg-muted border border-transparent rounded-xl focus:outline-none focus:bg-white focus:border-primary transition-all" />
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-3">
            <p class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">Menu</p>
            <a href="/"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium bg-primary/10 text-primary mb-1">
                <i class="fa-solid fa-house w-4 text-center"></i>
                Trang chủ
            </a>
            <a href="/shop"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors mb-1">
                <i class="fa-solid fa-display w-4 text-center"></i>
                Sản phẩm
            </a>
            <a href="#"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors mb-1">
                <i class="fa-solid fa-list w-4 text-center"></i>
                Danh mục
            </a>
            <a href="#"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors mb-1">
                <i class="fa-solid fa-star w-4 text-center"></i>
                Ưu đãi
            </a>

            <div class="border-t border-border my-4"></div>
            <p class="px-3 text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">Tài khoản</p>
            @if (!Auth::check())
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
            @else
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors mb-1">
                    <i class="fa-solid fa-user w-4 text-center"></i>
                    Hồ sơ
                </a>
                <form action="{{ route('auth.client.logout') }}" method="POST">
                    @csrf
                    <button
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                        Đăng xuất
                    </button>
                </form>
            @endif
        </nav>

        <div class="p-5 border-t border-border">
            <a href="/cart"
                class="flex items-center justify-center gap-2 w-full py-3 bg-primary text-white font-semibold rounded-xl hover:bg-primary/90 transition-colors">
                <i class="fa-solid fa-bag-shopping"></i>
                Giỏ hàng (3)
            </a>
        </div>
    </div>
</div>
