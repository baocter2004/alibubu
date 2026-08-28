@extends('admin.layouts.app')

@section('title', __('admin/tag.title.trash'))

@section('content')
    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/tag.title.trash') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/tag.subtitle.trash') }}</p>
            </div>
            <a href="{{ route('admin.tags.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left"></i>
                {{ __('common.actions.back') }}
            </a>
        </div>

        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[620px] w-full table-fixed admin-table">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[36%] px-4 py-3">{{ __('admin/tag.fields.name') }}</th>
                        <th class="w-[34%] px-4 py-3">{{ __('common.labels.deleted_at') }}</th>
                        <th class="w-[30%] text-center px-4 py-3">{{ __('common.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($tags as $tag)
                        <tr class="text-sm text-gray-700 transition-colors">
                            <td class="px-4 py-3 truncate">{{ $tag->name }}</td>
                            <td class="px-4 py-3">{{ $tag->deleted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-3">
                                    <form action="{{ route('admin.tags.restore', $tag->id) }}" method="POST"
                                        data-confirm="{{ __('common.confirm.restore_text') }}"
                                        data-confirm-title="{{ __('common.confirm.restore_title') }}">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800"
                                            title="{{ __('common.actions.restore') }}">
                                            <i class="fas fa-rotate-left"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.tags.force-destroy', $tag->id) }}" method="POST"
                                        data-confirm="{{ __('common.confirm.force_delete_text') }}"
                                        data-confirm-title="{{ __('common.confirm.force_delete_title') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700"
                                            title="{{ __('common.actions.force_delete') }}">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-16 text-center text-gray-500">
                                <i class="fas fa-trash text-4xl text-gray-300 block mb-3"></i>
                                <p class="font-medium text-gray-700">{{ __('common.empty.title') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $tags->withQueryString()])
    </div>
@endsection
