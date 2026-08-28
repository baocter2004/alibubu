@extends('admin.layouts.app')

@section('title', __('admin/category.title.index'))

@section('content')
    <div class="w-full mb-6 bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/category.title.index') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/category.subtitle.index') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.categories.trash') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-trash"></i>
                    <span class="hidden sm:inline">{{ __('common.labels.trash') }}</span>
                </a>
                <a href="{{ route('admin.categories.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-plus"></i>
                    {{ __('common.actions.create') }}
                </a>
            </div>
        </div>

        <form action="{{ route('admin.categories.index') }}" method="GET"
            class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="keyword"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.labels.keyword') }}</label>
                <input type="search" id="keyword" name="keyword" value="{{ request('keyword') }}"
                    class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="parent_id"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/category.fields.parent') }}</label>
                <select id="parent_id" name="parent_id"
                    class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('common.labels.all') }}</option>
                    @foreach ($parents as $id => $name)
                        <option value="{{ $id }}" @selected((string) request('parent_id') === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <div class="flex-1">
                    <label for="is_active"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.labels.status') }}</label>
                    <select id="is_active" name="is_active"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('common.labels.all') }}</option>
                        @foreach ($statuses as $key => $value)
                            <option value="{{ $key }}" @selected(request('is_active') !== null && (string) request('is_active') === (string) $key)>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="self-end px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-magnifying-glass"></i>
                </button>
                <a href="{{ route('admin.categories.index') }}"
                    class="self-end px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="w-full bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[960px] w-full table-fixed admin-table">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[6%] text-center px-4 py-3">{{ __('common.labels.id') }}</th>
                        <th class="w-[22%] px-4 py-3">{{ __('admin/category.fields.name') }}</th>
                        <th class="w-[18%] px-4 py-3">{{ __('admin/category.fields.slug') }}</th>
                        <th class="w-[16%] px-4 py-3">{{ __('admin/category.fields.parent') }}</th>
                        <th class="w-[9%] text-center px-4 py-3">{{ __('admin/category.fields.products_count') }}</th>
                        <th class="w-[9%] text-center px-4 py-3">{{ __('admin/category.fields.ordinal') }}</th>
                        <th class="w-[10%] text-center px-4 py-3">{{ __('common.labels.status') }}</th>
                        <th class="w-[10%] text-center px-4 py-3">{{ __('common.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($categories as $category)
                        <tr class="text-sm text-gray-700 transition-colors">
                            <td class="text-center px-4 py-3">{{ $category->id }}</td>
                            <td class="px-4 py-3">
                                <span class="flex items-center gap-2 truncate">
                                    @if ($category->icon)
                                        <i class="{{ $category->icon }} text-gray-400"></i>
                                    @endif
                                    {{ $category->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 truncate text-gray-500">{{ $category->slug }}</td>
                            <td class="px-4 py-3 truncate">{{ $category->parent?->name ?? '-' }}</td>
                            <td class="text-center px-4 py-3">{{ $category->products_count }}</td>
                            <td class="text-center px-4 py-3">{{ $category->ordinal }}</td>
                            <td class="text-center px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ \App\Const\GlobalConst::statusBadgeClass($category->is_active) }}">
                                    {{ \App\Const\GlobalConst::statusLabel($category->is_active) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.categories.show', $category->id) }}"
                                        class="text-blue-500 hover:text-blue-700" title="{{ __('common.actions.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                        class="text-yellow-500 hover:text-yellow-700"
                                        title="{{ __('common.actions.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
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
                            <td colspan="8" class="px-4 py-16 text-center text-gray-500">
                                <i class="fas fa-layer-group text-4xl text-gray-300 block mb-3"></i>
                                <p class="font-medium text-gray-700">{{ __('common.empty.title') }}</p>
                                <p class="text-sm">{{ __('common.empty.description') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $categories->withQueryString()])
    </div>
@endsection
