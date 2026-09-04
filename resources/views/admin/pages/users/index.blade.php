@extends('admin.layouts.app')

@section('title', __('admin/user.title.index'))

@section('content')
    <div class="w-full mb-6 bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/user.title.index') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/user.subtitle.index') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.trash') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-trash"></i>
                    <span class="hidden sm:inline">{{ __('common.labels.trash') }}</span>
                </a>
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>{{ __('common.actions.create') }}</span>
                </a>
            </div>
        </div>

        <form action="{{ route('admin.users.index') }}" method="GET" class="space-y-4">
            <div>
                <label for="keyword"
                    class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.labels.keyword') }}</label>
                <input type="search" id="keyword" name="keyword" value="{{ request('keyword') }}"
                    placeholder="{{ __('admin/user.fields.fullname') }}, {{ __('admin/user.fields.email') }}, {{ __('admin/user.fields.phone_number') }}"
                    class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-accent/30">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="fullname"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/user.fields.fullname') }}</label>
                    <input type="text" id="fullname" name="fullname" value="{{ request('fullname') }}"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-accent/30">
                </div>
                <div>
                    <label for="email"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/user.fields.email') }}</label>
                    <input type="text" id="email" name="email" value="{{ request('email') }}"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-accent/30">
                </div>
                <div>
                    <label for="phone_number"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/user.fields.phone_number') }}</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ request('phone_number') }}"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-accent/30">
                </div>
                <div>
                    <label for="role"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/user.fields.role') }}</label>
                    <select id="role" name="role"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-accent/30">
                        <option value="">{{ __('common.labels.all') }}</option>
                        @foreach ($roles as $key => $value)
                            <option value="{{ $key }}" @selected((string) request('role') === (string) $key)>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status"
                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/user.fields.status') }}</label>
                    <select id="status" name="status"
                        class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-accent/30">
                        <option value="">{{ __('common.labels.all') }}</option>
                        @foreach ($statuses as $key => $value)
                            <option value="{{ $key }}" @selected((string) request('status') === (string) $key)>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end items-center gap-2">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors">
                    <i class="fas fa-magnifying-glass"></i>
                    {{ __('common.actions.search') }}
                </button>
                @if (collect(request()->only(['keyword', 'fullname', 'email', 'phone_number', 'role', 'status']))->filter(fn($v) => $v !== null && $v !== '')->isNotEmpty())
                    <a href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-rotate-left"></i>
                        {{ __('common.actions.clear_filter') }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="w-full bg-white rounded-lg shadow-lg p-4 md:p-6">
        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[1100px] w-full table-fixed admin-table">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[6%] text-center px-4 py-3">{{ __('common.labels.id') }}</th>
                        <th class="w-[17%] px-4 py-3">{{ __('admin/user.fields.fullname') }}</th>
                        <th class="w-[12%] px-4 py-3">{{ __('admin/user.fields.phone_number') }}</th>
                        <th class="w-[19%] px-4 py-3">{{ __('admin/user.fields.email') }}</th>
                        <th class="w-[11%] px-4 py-3">{{ __('admin/user.fields.role') }}</th>
                        <th class="w-[12%] text-center px-4 py-3">{{ __('admin/user.fields.status') }}</th>
                        <th class="w-[10%] text-center px-4 py-3">{{ __('admin/user.fields.loyalty_points') }}</th>
                        <th class="w-[11%] px-4 py-3">{{ __('common.labels.created_at') }}</th>
                        <th class="w-[12%] text-center px-4 py-3">{{ __('common.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr class="text-sm text-gray-700 transition-colors">
                            <td class="text-center px-4 py-3">@include('components.id-badge', ['id' => $user->id])</td>
                            <td class="px-4 py-3 truncate">{{ $user->fullname }}</td>
                            <td class="px-4 py-3">{{ $user->phone_number ?: '-' }}</td>
                            <td class="px-4 py-3 truncate">{{ $user->email }}</td>
                            <td class="px-4 py-3">{{ \App\Const\UserConst::roleLabel($user->role) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ \App\Const\UserConst::statusBadgeClass($user->status) }}">
                                    {{ \App\Const\UserConst::statusLabel($user->status) }}
                                </span>
                            </td>
                            <td class="text-center px-4 py-3">{{ number_format($user->loyalty_points ?? 0) }}</td>
                            <td class="px-4 py-3">{{ $user->created_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                        class="text-primary hover:text-primary" title="{{ __('common.actions.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                        class="text-yellow-500 hover:text-yellow-700" title="{{ __('common.actions.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
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
                            <td colspan="9" class="px-4 py-16 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl text-gray-300 block mb-3"></i>
                                <p class="font-medium text-gray-700">{{ __('common.empty.title') }}</p>
                                <p class="text-sm">{{ __('common.empty.description') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $users->withQueryString()])
    </div>
@endsection
