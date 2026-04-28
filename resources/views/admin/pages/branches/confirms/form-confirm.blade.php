@extends('admin.layouts.app')

@section('title')
    {{ !empty($data['id']) ? 'Xác Nhận Cập Nhật' : 'Xác Nhận Thêm Mới' }}
@endsection

@section('content')
    <div class="max-w-5xl m-auto">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Kiểm tra thông tin chi nhánh</h1>
            <p class="text-gray-600 mt-1">Kiểm tra lại thông tin của chi nhánh.</p>
        </div>
        <form action="{{ route('admin.branches.save') }}" method="POST">
            @csrf
            @if (!empty($data['id']))
                <input type="hidden" name="id" value="{{ $data['id'] }}">
            @endif
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
                            Kiểm tra thông tin cơ bản của chi nhánh
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Tên Chi Nhánh</p>
                        <p class="text-gray-800 font-medium">{{ $data['name'] ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Mã Chi Nhánh</p>
                        <p class="text-gray-800 font-medium">{{ $data['slug'] ?? '-' }}</p>
                    </div>
                </div>

                @if (!empty($data['logo']))
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Logo Chi Nhánh</p>
                        <img src="{{ Storage::disk('public')->exists($data['logo']) ? Storage::url($data['logo']) : asset('/assets/images/default-shop.png') }}"
                            alt="Avatar" class="w-20 h-20 rounded-lg object-cover">
                    </div>
                @endif

                <div class="text-sm font-medium text-gray-700">
                    <i class="fa-solid fa-triangle-exclamation text-yellow-500 mr-1"></i>
                    Lưu ý: Mã chi nhánh sẽ được tự động tạo dựa trên tên chi nhánh và có thể chỉnh sửa sau khi đã tạo.
                    Vui lòng đảm bảo tên chi nhánh là duy nhất và phù hợp với quy định đặt tên của hệ thống.
                </div>
            </div>

            <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                        2
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">
                            Thông Tin Chi Tiết
                        </h2>
                        <p class="text-sm text-gray-500">
                            Kiểm tra thông tin bổ sung
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div class="px-2 py-1">
                        <p class="text-sm text-gray-500">Trạng thái</p>
                        @if (($data['is_active'] ?? null) == \App\Const\GlobalConst::IS_ACTIVE)
                            <span class="px-2 py-1 text-xs font-semibold text-green-600 bg-green-200 rounded-full">
                                Hoạt động
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-red-600 bg-red-200 rounded-full">
                                Không hoạt động
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Button --}}
            <div class="w-full p-4 mt-4 bg-white rounded-lg shadow-lg grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-4">
                <a href="{{ !empty($data['id']) ? route('admin.branches.edit', $data['id']) : route('admin.branches.create') }}"
                    class="w-full flex justify-center items-center gap-2 p-2 md:p-4 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 hover:text-gray-800 transition-all duration-200">
                    <i class="fa-solid fa-arrow-left"></i>
                    Quay Lại
                </a>

                <button type="submit"
                    class="w-full flex justify-center items-center gap-2 p-2 md:p-4 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-all duration-200">
                    {{ !empty($data['id']) ? 'Xác Nhận Cập Nhật' : 'Xác Nhận Thêm Mới' }}
                </button>
            </div>
        </form>
    </div>
@endsection
