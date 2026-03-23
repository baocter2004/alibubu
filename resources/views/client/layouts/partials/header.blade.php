<header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-border shadow-sm">
    <div class="hidden md:block bg-foreground text-white text-xs py-1.5">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <span><i class="fa-solid fa-truck mr-1"></i> Miễn phí vận chuyển cho đơn từ 500.000đ</span>
            <div class="flex gap-4">
                <a href="#" class="hover:text-primary transition-colors">Theo dõi đơn hàng</a>
                <a href="#" class="hover:text-primary transition-colors">Hỗ trợ</a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center gap-4 h-16">
            <button id="menu-open" class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg hover:bg-muted transition-colors" aria-label="Mở menu">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>
            <a href="/" class="flex items-center gap-2 shrink-0">
                <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white font-bold text-sm">A</div>
                <span class="font-bold text-lg text-foreground tracking-tight">Alibubu</span>
            </a>

            <nav class="hidden md:flex items-center gap-6 ml-4">
                <a href="/" class="text-sm font-medium text-foreground hover:text-primary transition-colors">Trang chủ</a>
                <a href="/shop" class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Sản phẩm</a>
                <a href="#" class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Danh mục</a>
                <a href="#" class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Ưu đãi</a>
            </nav>

            <div class="hidden md:flex flex-1 max-w-sm ml-auto relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground"></i>
                <input type="text" placeholder="Tìm kiếm sản phẩm..." class="w-full pl-9 pr-4 py-2 text-sm bg-muted border border-transparent rounded-full focus:outline-none focus:bg-white focus:border-primary transition-all"/>
            </div>

            <div class="flex items-center gap-1 ml-auto md:ml-2">
                <button class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg hover:bg-muted transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </button>

                <button class="hidden sm:flex items-center justify-center w-10 h-10 rounded-lg hover:bg-muted transition-colors relative">
                    <i class="fa-regular fa-heart text-lg"></i>
                </button>

                <a href="/cart" class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-muted transition-colors relative">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                    <span class="absolute -top-0.5 -right-0.5 bg-primary text-white text-[10px] font-bold min-w-4.5 h-4.5 rounded-full flex items-center justify-center px-1 border-2 border-white">3</span>
                </a>

                <a href="#" class="hidden sm:flex items-center gap-2 ml-1 px-3 py-1.5 rounded-lg hover:bg-muted transition-colors">
                    <i class="fa-regular fa-user text-base"></i>
                    <span class="text-sm font-medium">Đăng nhập</span>
                </a>
            </div>
        </div>
    </div>

</header>