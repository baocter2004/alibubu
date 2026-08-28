@extends('admin.layouts.app')

@section('title')
    Chi tiết Phường / Xã
@endsection

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">
                Chi tiết Phường / Xã
            </h1>
            <p class="text-gray-500 mt-1">
                Thông tin chi tiết danh sách xã trực thuộc.
            </p>
        </div>

        <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                    1
                </div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Thông tin chung
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Tên</p>
                    <p class="font-medium text-gray-800">
                        {{ $ward->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Code</p>
                    <p class="font-medium text-gray-800">
                        {{ $ward->code ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Code name</p>
                    <p class="font-medium text-gray-800">
                        {{ $ward->codename ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Loại</p>
                    <p class="font-medium text-gray-800">
                        {{ $ward->division_type ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Trực thuộc tỉnh</p>
                    <p class="font-medium text-gray-800">
                        {{ $ward->province->name ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Thời gian --}}
        <div class="w-full p-4 mt-4 mb-12 bg-white rounded-lg shadow-lg space-y-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-semibold">
                    2
                </div>
                <h2 class="text-lg font-semibold text-gray-800">
                    Thời gian
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Ngày tạo</p>
                    <p class="font-medium text-gray-800">
                        {{ optional($ward->created_at)->format('d/m/Y') ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Cập nhật lần cuối</p>
                    <p class="font-medium text-gray-800">
                        {{ optional($ward->updated_at)->format('d/m/Y') ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl shadow">
            <a href="{{ route('admin.wards.index') }}"
                class="flex justify-center items-center gap-2 py-3 text-sm font-medium
                      text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Quay lại danh sách
            </a>
        </div>

    </div>
@endsection
