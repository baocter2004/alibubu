@extends('admin.layouts.app')

@section('title', __('admin/tag.title.create'))

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/tag.title.create') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/tag.subtitle.create') }}</p>
        </div>

        @include('admin.pages.tags.form', ['tag' => null, 'formAction' => route('admin.tags.confirm')])
    </div>
@endsection
