@extends('admin.layouts.app')

@section('title')
    Trang thêm mới chi nhánh
@endsection

@section('content')
    <div class="max-w-5xl m-auto">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Thêm mới chi nhánh</h1>
            <p class="text-gray-600 mt-1">Điền đầy đủ thông tin để tạo chi nhánh mới.</p>
        </div>
        @include('admin.pages.branches.form', ['formAction' => route('admin.branches.confirm')])
    </div>
@endsection