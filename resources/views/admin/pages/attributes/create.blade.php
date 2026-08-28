@extends('admin.layouts.app')

@section('title', __('admin/attribute.title.create'))

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/attribute.title.create') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/attribute.subtitle.create') }}</p>
        </div>

        @include('admin.pages.attributes.form', [
            'attribute' => null,
            'formAction' => route('admin.attributes.confirm'),
        ])
    </div>
@endsection
