@extends('admin.layouts.app')

@section('title')
    Trang chỉnh sửa chi nhánh - {{ $branch->name ?? 'Chi Nhánh Mới' }}
@endsection

@section('content')
    <div class="max-w-5xl m-auto">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Chỉnh sửa chi nhánh</h1>
            <p class="text-gray-600 mt-1">Điền đầy đủ thông tin để chỉnh sửa chi nhánh.</p>
        </div>
        @include('admin.pages.branches.form', ['formAction' => route('admin.branches.confirm', $branch->id)])
    </div>
@endsection