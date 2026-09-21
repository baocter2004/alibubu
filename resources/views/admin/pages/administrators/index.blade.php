@extends('admin.layouts.app')

@section('title', __('admin/administrator.title.index'))

@section('content')
    <div class="w-full mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/administrator.title.index') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/administrator.subtitle.index') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('roles.manage')
                    <a href="{{ route('admin.roles.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fa-solid fa-key"></i>
                        {{ __('admin/nav.roles') }}
                    </a>
                @endcan
                <a href="{{ route('admin.administrators.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors">
                    <i class="fa-solid fa-plus"></i>
                    {{ __('common.actions.create') }}
                </a>
            </div>
        </div>
    </div>

    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[860px] w-full admin-table admin-table-sticky">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[24%] px-4 py-3">{{ __('admin/administrator.fields.name') }}</th>
                        <th class="w-[26%] px-4 py-3">{{ __('admin/administrator.fields.email') }}</th>
                        <th class="w-[17%] px-4 py-3">{{ __('admin/administrator.fields.role') }}</th>
                        <th class="w-[13%] px-4 py-3">{{ __('admin/administrator.fields.is_active') }}</th>
                        <th class="w-[13%] px-4 py-3">{{ __('admin/administrator.fields.created_at') }}</th>
                        <th class="w-[7%] text-center px-4 py-3">{{ __('common.labels.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($administrators as $administrator)
                        @php
                            $isSelf = (string) $administrator->id === (string) Auth::guard('admin')->id();
                            $manageable = $canManage($administrator);
                        @endphp
                        <tr class="text-sm text-gray-700 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $administrator->name }}
                                @if ($isSelf)
                                    <span class="ml-1 inline-flex items-center px-2 py-0.5 text-[11px] font-semibold rounded-full bg-gray-100 text-gray-600">{{ __('admin/administrator.you') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $administrator->email }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $administrator->isSuperAdmin() ? 'bg-primary text-white' : 'bg-primary-soft text-primary' }}">
                                    @if ($administrator->isSuperAdmin())
                                        <i class="fa-solid fa-crown text-[10px]"></i>
                                    @endif
                                    {{ $administrator->roleLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ \App\Const\GlobalConst::statusBadgeClass($administrator->isActive()) }}">
                                    <i class="fa-solid {{ $administrator->isActive() ? 'fa-circle-check' : 'fa-lock' }} text-[10px]"></i>
                                    {{ __('admin/administrator.status.' . ($administrator->isActive() ? 'active' : 'inactive')) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $administrator->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-3">
                                    @if ($manageable)
                                        <a href="{{ route('admin.administrators.edit', $administrator->id) }}"
                                            class="text-primary hover:text-primary-hover" title="{{ __('common.actions.edit') }}"
                                            aria-label="{{ __('common.actions.edit') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endif
                                    @if ($manageable && ! $isSelf)
                                        <form action="{{ route('admin.administrators.destroy', $administrator->id) }}" method="POST"
                                            data-confirm="{{ __('admin/administrator.confirm.delete_text') }}"
                                            data-confirm-title="{{ __('admin/administrator.confirm.delete_title') }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700" title="{{ __('common.actions.delete') }}"
                                                aria-label="{{ __('common.actions.delete') }}">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @unless ($manageable)
                                        <span class="text-gray-300" title="{{ __('admin/administrator.hints.staff_only') }}">
                                            <i class="fa-solid fa-lock"></i>
                                        </span>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center text-gray-500">
                                <i class="fa-solid fa-user-shield text-4xl text-gray-300 block mb-3"></i>
                                <p class="font-medium text-gray-700">{{ __('admin/administrator.empty') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $administrators])
    </div>
@endsection
