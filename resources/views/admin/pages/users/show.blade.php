@extends('admin.layouts.app')

@section('title')
    Trang chi tiết người dùng
@endsection

@section('content')
    <div class="max-w-5xl m-auto">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Kiểm tra thông tin người dùng</h1>
            <p class="text-gray-600 mt-1">Kiểm tra lại thông tin của người dùng.</p>
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
                        Kiểm tra thông tin cá nhân của người dùng
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Họ và tên</p>
                    <p class="text-gray-800 font-medium">{{ $user->fullname ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="text-gray-800 font-medium">{{ $user->email ?? '-' }}</p>
                </div>
            </div>

            <div>
                <p class="text-sm text-gray-500">Số điện thoại</p>
                <p class="text-gray-800 font-medium">{{ $user->phone_number ?? '-' }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Mật khẩu</p>
                    <p class="text-gray-800 font-medium">********************</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Xác nhận mật khẩu</p>
                    <p class="text-gray-800 font-medium">********************</p>
                </div>
            </div>

            <div class="bg-amber-100 rounded-lg p-3 text-yellow-800 flex justify-center items-center gap-2 mt-2">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Mật Khẩu Sẽ Được Ẩn Đi Vì Lý Do Bảo Mật
            </div>
        </div>

        {{-- Thông Tin Cá Nhân --}}
        <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                    2
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Thông Tin Cá Nhân
                    </h2>
                    <p class="text-sm text-gray-500">
                        Kiểm tra thông tin bổ sung
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Giới tính</p>
                    <p class="text-gray-800 font-medium">{{ \App\Const\UserConst::GENDER[$user->gender ?? ''] ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ngày sinh</p>
                    <p class="text-gray-800 font-medium">{{ $user->birthday ?? '-' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Vai trò</p>
                    <p class="text-gray-800 font-medium">{{ \App\Const\UserConst::ROLE[$user->role] ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Trạng thái</p>
                    <p class="text-gray-800 font-medium">{{ \App\Const\UserConst::STATUS[$user->status] ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Địa Chỉ Nhận Hàng --}}
        <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                    3
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Địa Chỉ Nhận Hàng</h2>
                    <p class="text-sm text-gray-500">Tối đa 5 địa chỉ (<span id="address-count">0</span>/5)</p>
                </div>
            </div>
            @foreach ($user->userAddresses ?? [] as $idx => $address)
                <div class="w-full p-6 bg-gray-50 border border-gray-200 rounded-lg relative group animate-fade-in">
                    <p class="text-sm text-gray-500">Họ và tên người nhận</p>
                    <p class="text-gray-800 font-medium">{{ $address->fullname ?? '-' }}</p>

                    <p class="text-sm text-gray-500 mt-2">Số điện thoại</p>
                    <p class="text-gray-800 font-medium">{{ $address->phone_number ?? '-' }}</p>

                    <p class="text-sm text-gray-500 mt-2">Tỉnh/Thành phố</p>
                    <p class="text-gray-800 font-medium">
                        {{ $address->province ?? '-' }}
                    </p>

                    <p class="text-sm text-gray-500 mt-2">Phường/Xã</p>
                    <p class="text-gray-800 font-medium">{{ $address->ward ?? '-' }}</p>

                    <p class="text-sm text-gray-500 mt-2">Địa chỉ chi tiết</p>
                    <p class="text-gray-800 font-medium">{{ $address->address ?? '-' }}</p>

                    <p class="text-sm text-gray-500 mt-2">Đặt làm địa chỉ mặc định</p>
                    <p
                        class="text-gray-800 font-medium px-2 py-1 rounded-lg inline-block {{ $address->is_default ? 'bg-blue-200' : 'bg-gray-50' }}">
                        {{ !empty($address->is_default) ? 'Có' : 'Không' }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Thông Tin Ngân Hàng --}}
        <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                    4
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Thông Tin Ngân Hàng
                    </h2>
                    <p class="text-sm text-gray-500">
                        Không bắt buộc
                    </p>
                </div>
            </div>

            <p class="text-sm text-gray-500">Tên ngân hàng</p>
            <p class="text-gray-800 font-medium">{{ $user->bank_name ?? '-' }}</p>

            <p class="text-sm text-gray-500 mt-2">Tên chủ tài khoản</p>
            <p class="text-gray-800 font-medium">{{ $user->user_bank_name ?? '-' }}</p>

            <p class="text-sm text-gray-500 mt-2">Số tài khoản</p>
            <p class="text-gray-800 font-medium">{{ $user->bank_account ?? '-' }}</p>
        </div>
    </div>
@endsection
