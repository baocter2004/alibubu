@extends('admin.layouts.app')

@section('title', __('admin/category.title.confirm'))

@section('content')
    <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/category.title.confirm') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/category.subtitle.confirm') }}</p>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
            @php
                $rows = [
                    ['label' => __('admin/category.fields.name'), 'value' => $data['name'] ?? '-'],
                    ['label' => __('admin/category.fields.slug'), 'value' => $data['slug'] ?? '-'],
                    ['label' => __('admin/category.fields.parent'), 'value' => $parents[$data['parent_id'] ?? null] ?? __('admin/category.no_parent')],
                    ['label' => __('admin/category.fields.icon'), 'value' => $data['icon'] ?: '-'],
                    ['label' => __('admin/category.fields.ordinal'), 'value' => $data['ordinal'] ?? 0],
                    ['label' => __('admin/category.fields.is_active'), 'value' => \App\Const\GlobalConst::statusLabel($data['is_active'] ?? null)],
                ];
            @endphp

            @foreach ($rows as $row)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ $row['label'] }}</dt>
                    <dd class="text-gray-800 font-medium break-words">{{ $row['value'] }}</dd>
                </div>
            @endforeach
        </dl>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ !empty($data['id']) ? route('admin.categories.edit', $data['id']) : route('admin.categories.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('common.actions.back') }}
            </a>

            <form action="{{ route('admin.categories.save') }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fa-solid fa-floppy-disk"></i>
                    {{ __('common.actions.save') }}
                </button>
            </form>
        </div>
    </div>
@endsection
