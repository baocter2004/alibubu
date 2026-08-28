@extends('admin.layouts.app')

@section('title', __('admin/user.title.trash'))

@section('content')
    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/user.title.trash') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/user.subtitle.trash') }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left"></i>
                {{ __('common.actions.back') }}
            </a>
        </div>

        <form action="{{ route('admin.users.trash') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="md:col-span-2">
                <label for="keyword"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.labels.keyword') }}</label>
                <input type="search" id="keyword" name="keyword" value="{{ request('keyword') }}"
                    class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="role"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/user.fields.role') }}</label>
                <select id="role" name="role"
                    class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('common.labels.all') }}</option>
                    @foreach ($roles as $key => $value)
                        <option value="{{ $key }}" @selected((string) request('role') === (string) $key)>{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-magnifying-glass"></i>
                    {{ __('common.actions.search') }}
                </button>
                <a href="{{ route('admin.users.trash') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>

        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[900px] w-full table-fixed admin-table">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[6%] text-center px-4 py-3">{{ __('common.labels.id') }}</th>
                        <th class="w-[22%] px-4 py-3">{{ __('admin/user.fields.fullname') }}</th>
                        <th class="w-[14%] px-4 py-3">{{ __('admin/user.fields.phone_number') }}</th>
                        <th class="w-[22%] px-4 py-3">{{ __('admin/user.fields.email') }}</th>
                        <th class="w-[12%] px-4 py-3">{{ __('admin/user.fields.role') }}</th>
                        <th class="w-[14%] px-4 py-3">{{ __('common.labels.deleted_at') }}</th>
                        <th class="w-[10%] text-center px-4 py-3">{{ __('common.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr class="text-sm text-gray-700 transition-colors">
                            <td class="text-center px-4 py-3">{{ $user->id }}</td>
                            <td class="px-4 py-3 truncate">{{ $user->fullname }}</td>
                            <td class="px-4 py-3">{{ $user->phone_number ?: '-' }}</td>
                            <td class="px-4 py-3 truncate">{{ $user->email }}</td>
                            <td class="px-4 py-3">{{ \App\Const\UserConst::roleLabel($user->role) }}</td>
                            <td class="px-4 py-3">{{ $user->deleted_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-3">
                                    <form action="{{ route('admin.users.restore', $user->id) }}" method="POST"
                                        data-confirm="{{ __('common.confirm.restore_text') }}"
                                        data-confirm-title="{{ __('common.confirm.restore_title') }}">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800"
                                            title="{{ __('common.actions.restore') }}">
                                            <i class="fas fa-rotate-left"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.force-destroy', $user->id) }}" method="POST"
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
                            <td colspan="7" class="px-4 py-16 text-center text-gray-500">
                                <i class="fas fa-trash text-4xl text-gray-300 block mb-3"></i>
                                <p class="font-medium text-gray-700">{{ __('common.empty.title') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $users->withQueryString()])
    </div>
@endsection
