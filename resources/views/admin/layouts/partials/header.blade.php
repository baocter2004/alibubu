@php
    $admin = Auth::guard('admin')->user();
@endphp

<header class="flex justify-between items-center px-4 md:px-6 py-3 bg-white shadow-md gap-4">
    <button type="button" id="sidebarToggle"
        class="navbar-btn flex items-center gap-2 text-gray-700 focus:outline-none cursor-pointer">
        <i class="fa-solid fa-bars text-lg"></i>
    </button>

    <form action="{{ route('admin.users.index') }}" method="GET" class="hidden md:flex flex-1 max-w-md relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <i class="fa-solid fa-magnifying-glass"></i>
        </span>
        <input type="search" name="keyword" value="{{ request('keyword') }}"
            placeholder="{{ __('admin/nav.search_placeholder') }}"
            class="w-full border border-gray-300 rounded-full py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </form>

    <div class="flex items-center gap-3 ml-auto">
        <x-locale-switcher />

        <a href="{{ route('index') }}" target="_blank" rel="noopener"
            class="hidden sm:flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-blue-600 transition-colors">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
            <span class="hidden lg:inline">{{ __('admin/nav.view_site') }}</span>
        </a>

        <div class="relative" id="buttonDropdown">
            <button type="button" class="flex border-l cursor-pointer border-gray-200 items-center px-2 gap-3">
                <span
                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center border border-gray-200 text-blue-600 font-bold shadow-inner">
                    {{ Str::upper(Str::substr($admin?->name ?? 'A', 0, 1)) }}
                </span>
                <span class="hidden sm:block text-left">
                    <span class="block text-sm font-medium text-gray-700">{{ $admin?->name }}</span>
                    <span class="block text-xs text-gray-500">{{ __('admin/nav.role') }}</span>
                </span>
            </button>

            <div id="dropDownMenu"
                class="absolute right-0 mt-2 w-52 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-50 hidden">
                <p class="px-4 py-2 border-b border-gray-100">
                    <span class="block text-sm font-medium text-gray-700">{{ $admin?->name }}</span>
                    <span class="block text-xs text-gray-500 truncate">{{ $admin?->email }}</span>
                </p>

                <form action="{{ route('auth.admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 text-left px-4 py-2 text-slate-700 hover:bg-red-50 hover:text-red-600 transition">
                        <i class="fa-solid fa-right-from-bracket w-4"></i>
                        {{ __('admin/nav.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

@push('scripts')
    <script>
        $(function() {
            $('#buttonDropdown').on('click', function(e) {
                e.stopPropagation();
                $('#dropDownMenu').toggleClass('hidden');
            });

            $(document).on('click', function() {
                $('#dropDownMenu').addClass('hidden');
            });
        });
    </script>
@endpush
