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
        <div class="flex justify-between items-center gap-4 h-16">
            <div class="flex items-center gap-4">
                <button id="menu-open"
                    class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg hover:bg-muted transition-colors"
                    aria-label="Mở menu">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <a href="/" class="flex items-center gap-2 shrink-0">
                    <div
                        class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white font-bold text-sm">
                        A</div>
                    <span class="font-bold text-lg text-foreground tracking-tight">Alibubu</span>
                </a>

                <nav class="hidden md:flex items-center gap-6 ml-4">
                    <a href="/"
                        class="text-sm font-medium text-foreground hover:text-primary transition-colors">Trang
                        chủ</a>
                    <a href="/shop"
                        class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Sản
                        phẩm</a>
                    <a href="#"
                        class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Danh
                        mục</a>
                    <a href="#"
                        class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Ưu
                        đãi</a>
                </nav>
            </div>

            <div class="flex items-center gap-1 ml-auto md:ml-2">
                <button
                    class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-muted transition-colors relative">
                    <i class="fa-regular fa-heart text-lg"></i>
                </button>

                <a href="/cart"
                    class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-muted transition-colors relative">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                    <span
                        class="absolute -top-0.5 -right-0.5 bg-primary text-white text-[10px] font-bold min-w-4.5 h-4.5 rounded-full flex items-center justify-center px-1 border-2 border-white">3</span>
                </a>

                @if (Auth::check())
                    <div class="hidden md:flex">
                        <a href="#"
                            class="items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-muted transition-colors">
                            <i class="fa-regular fa-user text-base"></i>
                            <span class="text-sm font-medium">{{ Auth::user()->fullname ?? 'Người dùng' }}</span>
                        </a>
                        <form action="{{ route('auth.client.logout') }}" method="POST">
                            @csrf
                            <button
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center">
                        <a href="{{ route('auth.client.showFormLogin') }}"
                            class="hidden sm:flex items-center gap-2 ml-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                            <i class="fa-regular fa-user w-4 text-center"></i>
                            Đăng nhập
                        </a>

                        <a href="{{ route('auth.client.showFormRegister') }}"
                            class="hidden sm:flex items-center gap-2 ml-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            <i class="fa-solid fa-user-plus w-4 text-center"></i>
                            Đăng ký
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

</header>
