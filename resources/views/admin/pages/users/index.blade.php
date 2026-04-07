@extends('admin.layouts.app')

@section('title')
    Trang Danh Sách Người Dùng
@endsection

@section('content')
    <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg">
        <form action="{{ route('admin.users.index') }}" method="GET">
            <div class="flex flex-col md:flex-row justify-end items-center">
                <a href="{{ route('admin.users.create') }}"
                    class="px-4 py-2 text-sm flex items-center font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                    <i class="fas fa-plus m-0 md:mr-2"></i>
                    <span>
                        Thêm Người Dùng
                    </span>
                </a>
            </div>
            <div class="w-full px-4 py-2 mb-2 mt-2">
                <label for="keyword" class="block text-sm font-medium text-gray-700 mb-1">Tìm Kiếm Người Dùng</label>
                <input type="text" id="keyword" name="keyword" value="{{ old('keyword', request('keyword')) }}"
                    placeholder="Tìm kiếm theo từ khóa"
                    class="w-full border border-gray-300 rounded-md p-2 
                   focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 px-4 py-2 mt-4">
                <div class="w-full">
                    <label for="fullname" class="block text-sm font-medium text-gray-700 mb-1">Tên Người Dùng</label>
                    <input type="text" id="fullname" name="fullname" value="{{ old('fullname', request('fullname')) }}"
                        placeholder="Nhập tên người dùng"
                        class="w-full border border-gray-300 rounded-md p-2 
                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-full">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', request('email')) }}"
                        placeholder="Nhập email người dùng"
                        class="w-full border border-gray-300 rounded-md p-2 
                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-full">
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Số Điện Thoại</label>
                    <input type="text" id="phone_number" name="phone_number"
                        value="{{ old('phone_number', request('phone_number')) }}"
                        placeholder="Nhập số điện thoại người dùng"
                        class="w-full border border-gray-300 rounded-md p-2 
                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="w-full flex justify-end items-center px-4 py-2 mt-4">
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                    Tìm Kiếm
                </button>
                @if (request()->filled('keyword') ||
                        request()->filled('fullname') ||
                        request()->filled('email') ||
                        request()->filled('phone_number'))
                    <a href="{{ route('admin.users.index') }}"
                        class="ml-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Xóa Bộ Lọc
                    </a>
                @endif
            </div>
        </form>
    </div>
    <div class="w-full p-4 bg-white shadow-lg">
        <div class="w-full px-4 py-2 mb-4">
            <h1 class="text-2xl font-semibold text-gray-900">Danh Sách Người Dùng</h1>
        </div>
        <div class="w-full overflow-x-auto rounded-2xl">
            <table class="min-w-[1280px] w-full table-fixed px-4 py-2 border border-gray-200 overflow-hidden">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[5%] text-center px-4 py-3">ID</th>
                        <th class="w-[15%] px-4 py-3">Tên Người Dùng</th>
                        <th class="w-[10%] px-4 py-3">Sđt</th>
                        <th class="w-[20%] px-4 py-3">Email</th>
                        <th class="w-[10%] px-4 py-3">Vai Trò</th>
                        <th class="w-[10%] px-4 py-3">Trạng Thái</th>
                        <th class="w-[10%] text-center px-4 py-3">Điểm Số</th>
                        <th class="w-[10%] px-4 py-3">Ngày Tạo</th>
                        <th class="w-[10%] text-center px-4 py-3">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr class="text-sm text-gray-700 hover:bg-blue-100">
                            <td class="text-center px-4 py-3">{{ $user->id }}</td>
                            <td class="px-4 py-3 text-ellipsis whitespace-nowrap overflow-hidden">{{ $user->fullname }}</td>
                            <td class="px-4 py-3">{{ $user->phone_number }}</td>
                            <td class="px-4 py-3 text-ellipsis whitespace-nowrap overflow-hidden">{{ $user->email }}</td>
                            <td class="px-4 py-3">{{ \App\Const\UserConst::ROLE[$user->role] ?? 'Unknown' }}</td>
                            <td class="px-4 py-3">
                                @if ($user->status)
                                    <span
                                        class="px-2 py-1 text-xs font-semibold text-green-600 bg-green-200 rounded-full">Hoạt
                                        động</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-red-600 bg-red-200 rounded-full">Không
                                        hoạt động</span>
                                @endif
                            </td>
                            <td class="text-center px-4 py-3">{{ $user->loyalty_points ?? 0 }}</td>
                            <td class="px-4 py-3">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="flex justify-center gap-2 items-center text-center px-4 py-3">
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                    class="text-blue-500 hover:text-blue-700" title="Xem">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="" class="text-yellow-500 hover:text-yellow-700" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Xóa"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">

                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr colspan="9" class="text-center text-gray-500">
                            <td class="px-4 py-3">Không có người dùng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @include('components.pagination', ['paginator' => $users])
        </div>
    </div>
@endsection
