@extends('admin.layouts.app')

@section('title')
    Trang chi tiết chi nhánh
@endsection

@section('content')
    <div class="max-w-5xl m-auto">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Kiểm tra thông tin chi nhánh</h1>
            <p class="text-gray-600 mt-1">Kiểm tra lại thông tin của chi nhánh.</p>
        </div>

        <div class="flex justify-center items-center">
            <img src="{{ Storage::exists($branch->logo) ? Storage::url($branch->logo) : asset('/assets/images/default-shop.png') }}" alt="Branch Image" class="w-48 h-48 object-cover rounded-full border-4 border-gray-300">
        </div>

        {{-- Thông Tin Cơ Bản --}}
        <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                    1
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Thông Tin Cơ Bản
                    </h2>
                    <p class="text-sm text-gray-500">
                        Kiểm tra thông tin chi tiết của chi nhánh
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Tên Chi Nhánh</p>
                    <p class="text-gray-800 font-medium">{{ $branch->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Mã Chi Nhánh</p>
                    <p class="text-gray-800 font-medium">{{ $branch->slug ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                    2
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Thông Tin Trạng thái Và Ngày Tháng
                    </h2>
                    <p class="text-sm text-gray-500">
                        Kiểm tra thông tin bổ sung
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div class="px-2 py-1">
                    <p class="text-sm text-gray-500">Trạng thái</p>
                    @if ($branch->is_active === \App\Const\GlobalConst::IS_ACTIVE)
                        <span class="px-2 py-1 text-xs font-semibold text-green-600 bg-green-200 rounded-full">
                            Hoạt động
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold text-red-600 bg-red-200 rounded-full">
                            Không hoạt động
                        </span>
                    @endif
                </div>
                <div class="px-2 py-1">
                    <p class="text-sm text-gray-500">Ngày tạo</p>
                    <p class="font-medium text-gray-800">
                        {{ optional($branch->created_at)->format('d/m/Y') ?? '-' }}
                    </p>
                </div>

                <div class="px-2 py-1">
                    <p class="text-sm text-gray-500">Cập nhật lần cuối</p>
                    <p class="font-medium text-gray-800">
                        {{ optional($branch->updated_at)->format('d/m/Y') ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="w-full p-4 mt-4 bg-white rounded-lg shadow-lg grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-4">

            <!-- Back -->
            <a href="{{ route('admin.branches.index') }}"
                class="w-full flex justify-center items-center gap-2 p-2 md:p-4 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 hover:text-gray-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300">

                <i class="fa-solid fa-arrow-left"></i>
                Quay Lại Danh Sách
            </a>

            <!-- Submit -->
            <a href="{{ route('admin.branches.edit', $branch->id) }}"
                class="w-full flex justify-center items-center gap-2 p-2 md:p-4 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">

                <i class="fa-solid fa-edit"></i>
                Chỉnh Sửa chi nhánh
            </a>

        </div>
    </div>
@endsection
