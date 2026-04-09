@extends('admin.layouts.app')

@section('title')
    Trang thêm mới người dùng
@endsection

@section('content')
    <div class="max-w-5xl m-auto">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Thêm mới người dùng</h1>
            <p class="text-gray-600 mt-1">Điền đầy đủ thông tin để tạo tài khoản mới.</p>
        </div>
        @include('admin.pages.users.form', ['formAction' => route('admin.users.confirm')])
    </div>
@endsection
