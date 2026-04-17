@extends('admin.layouts.app')

@section('title')
    Chi tiết Tỉnh / Thành phố
@endsection

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">
                Chi tiết Tỉnh / Thành phố
            </h1>
            <p class="text-gray-500 mt-1">
                Thông tin chi tiết và danh sách xã trực thuộc.
            </p>
        </div>

        <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                    1
                </div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Thông tin cơ bản
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Tên</p>
                    <p class="font-medium text-gray-800">
                        {{ $province->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Code</p>
                    <p class="font-medium text-gray-800">
                        {{ $province->code ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                    2
                </div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Thông tin bổ sung
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Code name</p>
                    <p class="font-medium text-gray-800">
                        {{ $province->codename ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Mã điện thoại</p>
                    <p class="font-medium text-gray-800">
                        {{ $province->phone_code ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Thời gian --}}
        <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                    3
                </div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Thời gian
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Ngày tạo</p>
                    <p class="font-medium text-gray-800">
                        {{ optional($province->created_at)->format('d/m/Y') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Cập nhật lần cuối</p>
                    <p class="font-medium text-gray-800">
                        {{ optional($province->updated_at)->format('d/m/Y') ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                        4
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">
                            Danh sách xã
                        </h2>
                        <p class="text-sm text-gray-500">
                            Tổng: {{ $wards->total() }} xã
                        </p>
                    </div>
                </div>
            </div>

            <div class="w-full overflow-x-auto rounded-2xl">
                <table class="min-w-4xl w-full table-fixed px-4 py-2 border border-gray-200 overflow-hidden">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                            <th class="text-center px-4 py-3 w-[5%]">ID</th>
                            <th class="px-4 py-3 w-[20%]">Tên</th>
                            <th class="px-4 py-3 w-[10%]">Code</th>
                            <th class="px-4 py-3 w-[15%]">Code name</th>
                            <th class="px-4 py-3 w-[15%]">Loại</th>
                            <th class="px-4 py-3 w-[15%]">Ngày tạo</th>
                            <th class="text-center px-4 py-3 w-[15%]">Hành động</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse ($wards as $ward)
                            <tr class="text-sm hover:bg-blue-50">
                                <td class="text-center px-4 py-3">{{ $ward->id }}</td>

                                <td class="px-4 py-3 truncate">
                                    {{ $ward->name }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $ward->code }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $ward->codename }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ ucfirst($ward->division_type) }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ optional($ward->created_at)->format('d/m/Y') }}
                                </td>

                                <td class="text-center px-4 py-3">
                                    <a href="{{ route('admin.wards.show', $ward->id) }}"
                                        class="text-blue-500 hover:text-blue-700" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-gray-500">
                                    Không có dữ liệu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                @include('components.pagination', ['paginator' => $wards])
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl shadow">
            <a href="{{ route('admin.provinces.index') }}"
                class="flex justify-center items-center gap-2 py-3 text-sm font-medium
                      text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Quay lại danh sách
            </a>
        </div>

    </div>
@endsection
