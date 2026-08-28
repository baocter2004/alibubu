@extends('admin.layouts.app')

@section('title', __('admin/category.title.show'))

@section('content')
    <div class="w-full bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $category->name }}</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.categories.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('common.actions.back') }}
                </a>
                <a href="{{ route('admin.categories.edit', $category->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-edit"></i>
                    {{ __('common.actions.edit') }}
                </a>
            </div>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @php
                $rows = [
                    ['label' => __('common.labels.id'), 'value' => $category->id],
                    ['label' => __('admin/category.fields.name'), 'value' => $category->name],
                    ['label' => __('admin/category.fields.slug'), 'value' => $category->slug],
                    ['label' => __('admin/category.fields.parent'), 'value' => $category->parent?->name ?? __('admin/category.no_parent')],
                    ['label' => __('admin/category.fields.icon'), 'value' => $category->icon ?: '-'],
                    ['label' => __('admin/category.fields.ordinal'), 'value' => $category->ordinal],
                    ['label' => __('admin/category.fields.products_count'), 'value' => $category->products_count],
                    ['label' => __('admin/category.fields.children_count'), 'value' => $category->children->count()],
                    ['label' => __('common.labels.status'), 'value' => \App\Const\GlobalConst::statusLabel($category->is_active)],
                    ['label' => __('common.labels.created_at'), 'value' => $category->created_at?->format('d/m/Y H:i')],
                    ['label' => __('common.labels.updated_at'), 'value' => $category->updated_at?->format('d/m/Y H:i')],
                ];
            @endphp

            @foreach ($rows as $row)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500 mb-1">{{ $row['label'] }}</dt>
                    <dd class="text-gray-800 font-medium break-words">{{ $row['value'] }}</dd>
                </div>
            @endforeach
        </dl>

        @if ($category->children->isNotEmpty())
            <div class="mt-8">
                <h2 class="font-semibold text-gray-900 mb-3">{{ __('admin/category.fields.children_count') }}</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($category->children as $child)
                        <a href="{{ route('admin.categories.show', $child->id) }}"
                            class="px-3 py-1.5 text-sm bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                            {{ $child->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
