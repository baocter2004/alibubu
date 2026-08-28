@extends('admin.layouts.app')

@section('title')
    Trang Danh Sách Các Thành Phố / Tỉnh
@endsection

@section('content')
    <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg">
        <form action="{{ route('admin.provinces.index') }}" method="GET">
            <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 px-4 py-2 mt-4">
                <div class="w-full">
                    <label for="keyword" class="block text-sm font-medium text-gray-700 mb-1">
                        Tìm Kiếm Thành Phố / Tỉnh
                    </label>
                    <input type="text" id="keyword" name="keyword" value="{{ old('keyword', request('keyword')) }}"
                        placeholder="Tìm kiếm theo từ khóa"
                        class="w-full border border-gray-300 rounded-md p-2 
                   focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-full">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên Thành Phố</label>
                    <input type="text" id="name" name="name" value="{{ old('name', request('name')) }}"
                        placeholder="Nhập tên thành phố"
                        class="w-full border border-gray-300 rounded-md p-2 
                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-full">
                    <label for="division_type" class="block text-sm font-medium text-gray-700 mb-1">Loại</label>
                    <select id="division_type" name="division_type"
                        class="w-full border border-gray-300 rounded-md p-2 
                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Vui lòng chọn loại</option>
                        <option value="tỉnh">Tỉnh</option>
                        <option value="thành phố trung ương">Thành Phố Trung Ương</option>
                    </select>
                </div>
            </div>
            <div class="w-full flex justify-end items-center px-4 py-2 mt-4">
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                    Tìm Kiếm
                </button>
                @if (request()->filled('keyword') || request()->filled('name') || request()->filled('division_type'))
                    <a href="{{ route('admin.provinces.index') }}"
                        class="ml-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Xóa Bộ Lọc
                    </a>
                @endif
            </div>
        </form>
    </div>
    <div class="w-full p-4 bg-white shadow-lg">
        <div class="w-full px-4 py-2 mb-4">
            <h1 class="text-2xl font-semibold text-gray-900">Danh Sách Thành Phố</h1>
        </div>
        <div class="w-full overflow-x-auto rounded-2xl">
            <table class="min-w-[1280px] w-full table-fixed px-4 py-2 border border-gray-200 overflow-hidden">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[5%] text-center px-4 py-3">ID</th>
                        <th class="w-[20%] px-4 py-3">Tên Thành Phố</th>
                        <th class="w-[5%] px-4 py-3">Code</th>
                        <th class="w-[10%] px-4 py-3">Code Name</th>
                        <th class="w-[10%] text-center px-4 py-3">Code Phone</th>
                        <th class="w-[15%] px-4 py-3">Loại</th>
                        <th class="w-[10%] text-center px-4 py-3">Số Xã</th>
                        <th class="w-[10%] text-center px-4 py-3">Sử Dụng</th>
                        <th class="w-[10%] px-4 py-3">Ngày Tạo</th>
                        <th class="w-[10%] text-center px-4 py-3">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($provinces as $province)
                        <tr class="text-sm text-gray-700 hover:bg-blue-100">
                            <td class="text-center px-4 py-3">{{ $province->id }}</td>
                            <td class="px-4 py-3 text-ellipsis whitespace-nowrap overflow-hidden">
                                {{ $province->name }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $province->code }}
                            </td>
                            <td class="px-4 py-3">{{ $province->codename }}</td>
                            <td class="text-center px-4 py-3">{{ $province->phone_code }}</td>
                            <td class="px-4 py-3">{{ $province->division_type }}</td>
                            <td class="text-center px-4 py-3">{{ $province->wards_count }}</td>
                            <td class="text-center px-4 py-3">{{ $province->user_addresses_count }}</td>
                            <td class="px-4 py-3">{{ $province->created_at->format('d/m/Y') }}</td>
                            <td class="flex justify-center gap-2 items-center text-center px-4 py-3">
                                <a href="{{ route('admin.provinces.show', $province->id) }}"
                                    class="text-blue-500 hover:text-blue-700" title="Xem">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="w-full h-40 text-center text-gray-500">
                            <td colspan="9" class="px-4 py-3">
                                Không có thành phố nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('components.pagination', ['paginator' => $provinces])
    </div>
@endsection
