@extends('admin.layouts.app')

@section('title', __('admin/tag.title.edit'))

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/tag.title.edit') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/tag.subtitle.edit') }}</p>
        </div>

        @include('admin.pages.tags.form', ['formAction' => route('admin.tags.confirm', $tag->id)])
    </div>
@endsection
