@extends('admin.layouts.app')

@section('title', __('admin/user.title.create'))

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/user.title.create') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/user.subtitle.create') }}</p>
        </div>

        @include('admin.pages.users.form', ['formAction' => route('admin.users.confirm')])
    </div>
@endsection
