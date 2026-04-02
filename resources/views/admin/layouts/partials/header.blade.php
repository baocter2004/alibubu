<header class="flex justify-between items-center px-6 py-5 bg-white shadow-md">
    <button type="button" id="sidebarToggle"
        class="navbar-btn flex items-center gap-2 text-gray-700 focus:outline-none cursor-pointer">
        <i class="fa-solid fa-bars text-lg"></i>
    </button>

    <div class="flex items-center gap-4">
        <div class="relative" id="buttonDropdown">
            <div class="flex border-l-4 border-r-4 cursor-pointer rounded-md border-foreground items-center px-2 gap-3">
                <div
                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold shadow-inner">
                    {{ strtoupper(substr(Auth::guard('admin')->name, 0, 1)) }}
                </div>
                <h2 class="text-slate-700 font-medium">
                    {{ Auth::guard('admin')->name ?? 'Admin' }}
                </h2>
            </div>
            <div id="dropDownMenu"
                class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-50 hidden">
                <a href=""
                    class="block px-4 py-2 text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    Thông tin cá nhân
                </a>
                <a href=""
                    class="block px-4 py-2 text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    Đổi mật khẩu
                </a>
                <form action="{{ route('auth.admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2 text-slate-700 hover:bg-red-50 hover:text-red-600 transition">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

@push('scripts')
    <script>
        const btn = document.getElementById("buttonDropdown");
        const menu = document.getElementById("dropDownMenu");

        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            menu.classList.toggle("hidden");
        });

        document.addEventListener("click", () => {
            menu.classList.add("hidden");
        });
    </script>
@endpush
