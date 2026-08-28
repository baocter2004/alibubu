@extends('admin.layouts.app')

@section('title', __('admin/product.title.edit'))

@section('content')
    <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
        <h1 class="text-xl md:text-2xl font-semibold text-gray-900 mb-6">{{ __('admin/product.title.edit') }}</h1>
        @include('admin.pages.products.form')
    </div>
@endsection
