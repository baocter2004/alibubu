@extends('admin.layouts.app')

@section('title', __('admin/attribute.title.index'))

@section('content')
    <div class="w-full mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/attribute.title.index') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/attribute.subtitle.index') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.attributes.trash') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-trash"></i>
                    <span class="hidden sm:inline">{{ __('common.labels.trash') }}</span>
                </a>
                <a href="{{ route('admin.attributes.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-plus"></i>
                    {{ __('common.actions.create') }}
                </a>
            </div>
        </div>

        <form action="{{ route('admin.attributes.index') }}" method="GET"
            class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-2">
                <label for="keyword"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.labels.keyword') }}</label>
                <input type="search" id="keyword" name="keyword" value="{{ request('keyword') }}"
                    class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="is_active"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.labels.status') }}</label>
                <select id="is_active" name="is_active"
                    class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('common.labels.all') }}</option>
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" @selected(request('is_active') !== null && (string) request('is_active') === (string) $key)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-magnifying-glass"></i>
                    {{ __('common.actions.search') }}
                </button>
                <a href="{{ route('admin.attributes.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[760px] w-full table-fixed admin-table">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[8%] text-center px-4 py-3">{{ __('common.labels.id') }}</th>
                        <th class="w-[28%] px-4 py-3">{{ __('admin/attribute.fields.name') }}</th>
                        <th class="w-[24%] px-4 py-3">{{ __('admin/attribute.fields.slug') }}</th>
                        <th class="w-[14%] text-center px-4 py-3">{{ __('admin/attribute.fields.values_count') }}</th>
                        <th class="w-[14%] text-center px-4 py-3">{{ __('common.labels.status') }}</th>
                        <th class="w-[12%] text-center px-4 py-3">{{ __('common.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($attributes as $attribute)
                        <tr class="text-sm text-gray-700 transition-colors">
                            <td class="text-center px-4 py-3">{{ $attribute->id }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 truncate">{{ $attribute->name }}</td>
                            <td class="px-4 py-3 text-gray-500 truncate">{{ $attribute->slug }}</td>
                            <td class="text-center px-4 py-3">{{ $attribute->values_count }}</td>
                            <td class="text-center px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ \App\Const\GlobalConst::statusBadgeClass($attribute->is_active) }}">
                                    {{ \App\Const\GlobalConst::statusLabel($attribute->is_active) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.attributes.show', $attribute->id) }}"
                                        class="text-blue-500 hover:text-blue-700" title="{{ __('common.actions.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.attributes.edit', $attribute->id) }}"
                                        class="text-yellow-500 hover:text-yellow-700" title="{{ __('common.actions.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.attributes.destroy', $attribute->id) }}" method="POST"
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
                            <td colspan="6" class="px-4 py-16 text-center text-gray-500">
                                <i class="fas fa-sliders text-4xl text-gray-300 block mb-3"></i>
                                <p class="font-medium text-gray-700">{{ __('common.empty.title') }}</p>
                                <p class="text-sm">{{ __('common.empty.description') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $attributes->withQueryString()])
    </div>
@endsection
