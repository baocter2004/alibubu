@extends('admin.layouts.app')

@section('title')
    Trang Danh Sách Chi Nhánh - Thùng Rác
@endsection

@section('content')
    <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg">
        <form action="{{ route('admin.branches.trash') }}" method="GET">
            <div class="w-full px-4 py-2 mb-2 mt-2">
                <label for="keyword" class="block text-sm font-medium text-gray-700 mb-1">Tìm Kiếm Chi Nhánh</label>
                <input type="text" id="keyword" name="keyword" value="{{ old('keyword', request('keyword')) }}"
                    placeholder="Tìm kiếm theo từ khóa"
                    class="w-full border border-gray-300 rounded-md p-2 
                   focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-4 px-4 py-2 mt-4">
                <div class="w-full">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên Chi Nhánh</label>
                    <input type="text" id="name" name="name" value="{{ old('name', request('name')) }}"
                        placeholder="Nhập tên Chi Nhánh"
                        class="w-full border border-gray-300 rounded-md p-2 
                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-full">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Mã Chi Nhánh</label>
                    <input type="slug" id="slug" name="slug" value="{{ old('slug', request('slug')) }}"
                        placeholder="Nhập mã Chi Nhánh"
                        class="w-full border border-gray-300 rounded-md p-2 
                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-full">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng Thái</label>
                    <select id="status" name="status"
                        class="w-full border border-gray-300 rounded-md p-2 
                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất Cả Trạng Thái</option>
                        @foreach ($statuses as $key => $value)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="w-full flex justify-end items-center px-4 py-2 mt-4">
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                    Tìm Kiếm
                </button>
                @if (request()->filled('keyword') ||
                        request()->filled('name') ||
                        request()->filled('slug') ||
                        request()->filled('status'))
                    <a href="{{ route('admin.branches.index') }}"
                        class="ml-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Xóa Bộ Lọc
                    </a>
                @endif
            </div>
        </form>
    </div>
    <div class="w-full p-4 bg-white shadow-lg">
        <div class="w-full px-4 py-2 mb-4">
            <h1 class="text-2xl font-semibold text-gray-900">Danh Sách Chi Nhánh</h1>
        </div>
        <div class="w-full overflow-x-auto rounded-2xl">
            <table class="min-w-[1280px] w-full table-fixed px-4 py-2 border border-gray-200 overflow-hidden">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[5%] text-center px-4 py-3">ID</th>
                        <th class="w-[15%] text-center px-4 py-3">Ảnh Đại Diện</th>
                        <th class="w-[15%] px-4 py-3">Tên Chi Nhánh</th>
                        <th class="w-[15%] px-4 py-3">Mã Chi Nhánh</th>
                        <th class="w-[10%] text-center px-4 py-3">Trạng Thái</th>
                        <th class="w-[15%] text-center px-4 py-3">Số Sản Phẩm</th>
                        <th class="w-[10%] px-4 py-3">Ngày Tạo</th>
                        <th class="w-[15%] text-center px-4 py-3">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($branches as $branch)
                        <tr class="text-sm text-gray-700 hover:bg-blue-100">
                            <td class="text-center px-4 py-3">{{ $branch->id }}</td>
                            <td class="text-center px-4 py-3">
                                <img src="{{ Storage::disk('public')->exists($branch->logo) ? Storage::url($branch->logo) : asset('/assets/images/default-shop.png') }}"
                                    alt="Avatar" class="w-12 h-12 rounded-full object-cover mx-auto">
                            </td>
                            <td class="px-4 py-3 text-ellipsis whitespace-nowrap overflow-hidden">
                                {{ $branch->name }}
                            </td>
                            <td class="px-4 py-3 text-ellipsis whitespace-nowrap overflow-hidden">
                                {{ $branch->slug }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($branch->is_active === \App\Const\GlobalConst::IS_ACTIVE)
                                    <span class="px-2 py-1 text-xs font-semibold text-green-600 bg-green-200 rounded-full">
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-red-600 bg-red-200 rounded-full">
                                        Không hoạt động
                                    </span>
                                @endif
                            </td>
                            <td class="text-center px-4 py-3">{{ $branch->products_count ?? 0 }}</td>
                            <td class="px-4 py-3">{{ $branch->created_at?->format('d/m/Y') ?? 'Chưa Khởi Tạo' }}</td>
                            <td class="text-center px-4 py-3">
                                <div class="flex justify-center gap-2 items-center ">
                                    <form action="{{ route('admin.branches.restore', $branch->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Khôi phục">

                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.branches.destroy', $branch->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Xóa"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa Chi Nhánh này?')">

                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="w-full h-40 text-center text-gray-500">
                            <td colspan="9" class="px-4 py-3">
                                Không có Chi Nhánh nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('components.pagination', ['paginator' => $branches])
    </div>
@endsection
