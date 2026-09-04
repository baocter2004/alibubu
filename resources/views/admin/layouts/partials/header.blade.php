@php
    $admin = Auth::guard('admin')->user();
@endphp

<header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-gray-200">
    <div class="flex items-center gap-3 px-4 md:px-6 py-3">
        <button type="button" id="sidebarToggle"
            class="w-10 h-10 shrink-0 flex items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 transition-colors">
            <i class="fa-solid fa-bars"></i>
        </button>

        <form action="{{ route('admin.products.index') }}" method="GET" class="hidden md:block flex-1 max-w-md">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                <input type="search" name="keyword" value="{{ request('keyword') }}"
                    placeholder="{{ __('admin/nav.search_placeholder') }}"
                    class="w-full pl-9 pr-4 py-2 text-sm bg-gray-100 border border-transparent rounded-xl focus:outline-none focus:bg-white focus:border-blue-400 transition-all">
            </div>
        </form>

        <div class="flex items-center gap-2 ml-auto">
            @include('components.locale-switcher')

            <a href="{{ route('index') }}" target="_blank" rel="noopener"
                class="hidden sm:inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span class="hidden xl:inline">{{ __('admin/nav.view_site') }}</span>
            </a>

            <div class="relative" id="buttonDropdown">
                <button type="button"
                    class="flex items-center gap-3 pl-3 py-1.5 pr-2 rounded-lg border-l border-gray-200 hover:bg-gray-100 transition-colors">
                    <span
                        class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center">
                        {{ Str::upper(Str::substr($admin?->name ?? 'A', 0, 1)) }}
                    </span>
                    <span class="hidden sm:block text-left leading-tight">
                        <span class="block text-sm font-medium text-gray-800">{{ $admin?->name }}</span>
                        <span class="block text-xs text-gray-500">{{ __('admin/nav.role') }}</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 hidden sm:block"></i>
                </button>

                <div id="dropDownMenu"
                    class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg py-2 z-50 hidden">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $admin?->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $admin?->email }}</p>
                    </div>

                    <a href="{{ route('admin.profile.edit') }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 transition-colors">
                        <i class="fa-solid fa-user w-4"></i>
                        {{ __('admin/nav.profile') }}
                    </a>

                    <a href="{{ route('admin.profile.edit') }}#password"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 transition-colors">
                        <i class="fa-solid fa-key w-4"></i>
                        {{ __('admin/nav.change_password') }}
                    </a>

                    <a href="{{ route('index') }}" target="_blank" rel="noopener"
                        class="sm:hidden flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square w-4"></i>
                        {{ __('admin/nav.view_site') }}
                    </a>

                    <span class="block my-1 border-t border-gray-100"></span>

                    <form action="{{ route('auth.admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-2 text-left px-4 py-2 text-sm text-slate-700 hover:bg-red-50 hover:text-red-600 transition-colors">
                            <i class="fa-solid fa-right-from-bracket w-4"></i>
                            {{ __('admin/nav.logout') }}
                        </button>
                    </form>
                </div>
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
