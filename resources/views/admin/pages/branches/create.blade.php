@extends('admin.layouts.app')

@section('title', __('admin/branch.title.create'))

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/branch.title.create') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/branch.subtitle.create') }}</p>
        </div>

        @include('admin.pages.branches.form', ['formAction' => route('admin.branches.confirm')])
    </div>
@endsection
