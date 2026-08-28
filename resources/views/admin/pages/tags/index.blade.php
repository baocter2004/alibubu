@extends('admin.layouts.app')

@section('title', __('admin/tag.title.index'))

@section('content')
    <div class="w-full mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/tag.title.index') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/tag.subtitle.index') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.tags.trash') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-trash"></i>
                    <span class="hidden sm:inline">{{ __('common.labels.trash') }}</span>
                </a>
                <a href="{{ route('admin.tags.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-plus"></i>
                    {{ __('common.actions.create') }}
                </a>
            </div>
        </div>

        <form action="{{ route('admin.tags.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="search" name="keyword" value="{{ request('keyword') }}"
                placeholder="{{ __('common.labels.keyword') }}"
                class="flex-1 border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit"
                class="px-5 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                <i class="fas fa-magnifying-glass mr-1"></i>{{ __('common.actions.search') }}
            </button>
            <a href="{{ route('admin.tags.index') }}"
                class="px-5 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors text-center">
                <i class="fas fa-rotate-left"></i>
            </a>
        </form>
    </div>

    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[680px] w-full table-fixed admin-table">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[10%] text-center px-4 py-3">{{ __('common.labels.id') }}</th>
                        <th class="w-[32%] px-4 py-3">{{ __('admin/tag.fields.name') }}</th>
                        <th class="w-[28%] px-4 py-3">{{ __('admin/tag.fields.slug') }}</th>
                        <th class="w-[16%] text-center px-4 py-3">{{ __('admin/tag.fields.products_count') }}</th>
                        <th class="w-[14%] text-center px-4 py-3">{{ __('common.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($tags as $tag)
                        <tr class="text-sm text-gray-700 transition-colors">
                            <td class="text-center px-4 py-3 truncate">{{ $tag->id }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 truncate">{{ $tag->name }}</td>
                            <td class="px-4 py-3 text-gray-500 truncate">{{ $tag->slug }}</td>
                            <td class="text-center px-4 py-3">{{ $tag->products_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.tags.show', $tag->id) }}"
                                        class="text-blue-500 hover:text-blue-700" title="{{ __('common.actions.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.tags.edit', $tag->id) }}"
                                        class="text-yellow-500 hover:text-yellow-700" title="{{ __('common.actions.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST"
                                        data-confirm="{{ __('common.confirm.delete_text') }}"
                                        data-confirm-title="{{ __('common.confirm.delete_title') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700"
                                            title="{{ __('common.actions.delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center text-gray-500">
                                <i class="fas fa-tags text-4xl text-gray-300 block mb-3"></i>
                                <p class="font-medium text-gray-700">{{ __('common.empty.title') }}</p>
                                <p class="text-sm">{{ __('common.empty.description') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $tags->withQueryString()])
    </div>
@endsection
