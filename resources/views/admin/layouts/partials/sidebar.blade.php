<aside class="bg-[#1e1e2d] text-gray-300 w-64 min-h-screen fixed left-0 top-0 z-50 transition-all duration-300"
    id="sidebar">
    <div class="p-6 flex items-center justify-between border-b border-gray-700/50">
        <div>
            <h1 class="text-xl font-bold text-white tracking-wider">Alibubu</h1>
            <p class="text-[10px] text-blue-400 font-semibold uppercase">Enterprise Admin</p>
        </div>
        <button id="sidebarToggleMobile" class="lg:hidden text-gray-400 hover:text-white">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <nav class="mt-4 px-3">
        <div class="mb-4">
            <p class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Main</p>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'hover:bg-gray-800' }}">
                        <i class="fa-solid fa-chart-pie w-5"></i>
                        <span class="font-medium text-sm">Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="mb-4">
            <p class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Sales & Stock</p>
            <ul class="space-y-1">
                <li class="sidebar-dropdown {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <a href="javascript:void(0)"
                        class="dropdown-toggle flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-800 transition-all">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-box w-5"></i>
                            <span class="font-medium text-sm">Products</span>
                        </div>
                        <i
                            class="fa-solid fa-chevron-right text-[10px] transition-transform duration-300 arrow-icon"></i>
                    </a>

                    <ul class="submenu hidden mt-1 ml-4 border-l border-gray-700 space-y-1">
                        <li>
                            <a href="#"
                                class="block py-2 px-6 text-sm hover:text-white transition-colors {{ request()->routeIs('admin.products.index') ? 'text-blue-400 font-bold' : 'text-gray-500' }}">
                                All Products
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                class="block py-2 px-6 text-sm hover:text-white transition-colors {{ request()->routeIs('admin.products.create') ? 'text-blue-400 font-bold' : 'text-gray-500' }}">
                                Add New Product
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-dropdown {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <a href="javascript:void(0)"
                        class="dropdown-toggle flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-800 transition-all">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-cart-shopping w-5"></i>
                            <span class="font-medium text-sm">Orders</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[10px] arrow-icon"></i>
                    </a>
                    <ul class="submenu hidden mt-1 ml-4 border-l border-gray-700 space-y-1">
                        <li>
                            <a href="#"
                                class="block py-2 px-6 text-sm hover:text-white transition-colors {{ request()->routeIs('admin.orders.index') ? 'text-blue-400 font-bold' : 'text-gray-500' }}">
                                Pending
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                class="block py-2 px-6 text-sm hover:text-white transition-colors {{ request()->routeIs('admin.orders.index') ? 'text-blue-400 font-bold' : 'text-gray-500' }}">
                                Completed
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="mb-4">
            <p class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest">System</p>
            <ul class="space-y-1">
                <li class="sidebar-dropdown {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="javascript:void(0)"
                        class="dropdown-toggle flex items-center justify-between px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-lg' : 'hover:bg-gray-800' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-users w-5"></i>
                            <span class="font-medium text-sm">Users</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[10px] arrow-icon"></i>
                    </a>
                    <ul class="submenu hidden mt-1 ml-4 border-l border-gray-700 space-y-1">
                        <li>
                            <a href="{{ route('admin.users.index') }}"
                                class="block py-2 px-6 text-sm hover:text-white transition-colors {{ request()->routeIs('admin.users.index') ? 'text-blue-400 font-bold' : 'text-gray-500' }}">
                                <i class="fa-solid fa-list w-4"></i>
                                Index
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.create') }}"
                                class="block py-2 px-6 text-sm hover:text-white transition-colors {{ request()->routeIs('admin.users.create') ? 'text-blue-400 font-bold' : 'text-gray-500' }}">
                                <i class="fa-solid fa-plus w-4"></i>
                                Create
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.trash') }}"
                                class="block py-2 px-6 text-sm hover:text-white transition-colors {{ request()->routeIs('admin.users.trash') ? 'text-blue-400 font-bold' : 'text-gray-500' }}">
                                <i class="fa-solid fa-trash w-4"></i>
                                Trash
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="mb-4">
            <p class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Provinces And Wards</p>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.provinces.index') }}"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.provinces.index') ? 'bg-blue-600 text-white shadow-lg' : 'hover:bg-gray-800' }}">
                        <i class="fa-solid fa-map w-5"></i>
                        <span class="font-medium text-sm">Provinces</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.wards.index') }}"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.wards.index') ? 'bg-blue-600 text-white shadow-lg' : 'hover:bg-gray-800' }}">
                        <i class="fa-solid fa-map-location-dot w-5"></i>
                        <span class="font-medium text-sm">Wards</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</aside>

<div id="overlay"></div>
