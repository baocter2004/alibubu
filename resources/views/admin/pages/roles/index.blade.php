@extends('admin.layouts.app')

@section('title', __('admin/role.title'))

@section('content')
    <div class="w-full mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/role.title') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/role.subtitle') }}</p>
                <p class="text-xs mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $usingDefaults ? 'bg-primary-soft text-primary' : 'bg-amber-50 text-amber-600' }}">
                    <i class="fa-solid {{ $usingDefaults ? 'fa-circle-check' : 'fa-pen' }} text-[10px]"></i>
                    {{ $usingDefaults ? __('admin/role.using_defaults') : __('admin/role.using_custom') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.roles.activity') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    {{ __('admin/role.actions.activity') }}
                </a>
                <form action="{{ route('admin.roles.reset') }}" method="POST"
                    data-confirm="{{ __('admin/role.confirm.reset_text') }}"
                    data-confirm-title="{{ __('admin/role.confirm.reset_title') }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fa-solid fa-rotate-left"></i>
                        {{ __('admin/role.actions.reset') }}
                    </button>
                </form>
            </div>
        </div>

        <p class="text-xs text-gray-500 mt-4 border-t border-gray-100 pt-4">
            <i class="fa-solid fa-circle-info mr-1"></i>{{ __('admin/role.baseline_hint') }}
        </p>
    </div>

    <form action="{{ route('admin.roles.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
            <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-[760px] w-full admin-table admin-table-sticky">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                            <th class="w-[46%] px-4 py-3">{{ __('admin/role.permission_column') }}</th>
                            <th class="w-[18%] text-center px-4 py-3">
                                {{ \App\Const\AdminConst::roleLabel(\App\Const\AdminConst::ROLE_SUPER_ADMIN) }}
                                <span class="block text-[10px] font-normal normal-case text-white/70">
                                    {{ trans_choice('admin/role.admins_count', $adminCounts[\App\Const\AdminConst::ROLE_SUPER_ADMIN] ?? 0, ['count' => $adminCounts[\App\Const\AdminConst::ROLE_SUPER_ADMIN] ?? 0]) }}
                                </span>
                            </th>
                            @foreach ($editableRoles as $role)
                                <th class="w-[18%] text-center px-4 py-3">
                                    {{ $roles[$role] ?? $role }}
                                    <span class="block text-[10px] font-normal normal-case text-white/70">
                                        {{ trans_choice('admin/role.admins_count', $adminCounts[$role] ?? 0, ['count' => $adminCounts[$role] ?? 0]) }}
                                    </span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($groups as $group => $permissions)
                            <tr class="bg-gray-50">
                                <td colspan="{{ 2 + count($editableRoles) }}" class="px-4 py-2 text-xs font-bold uppercase tracking-wide text-gray-500">
                                    {{ \App\Const\PermissionConst::groupLabel($group) }}
                                </td>
                            </tr>
                            @foreach ($permissions as $permission)
                                @php $locked = in_array($permission, $lockedPermissions, true); @endphp
                                <tr class="text-sm text-gray-700">
                                    <td class="px-4 py-3">{{ \App\Const\PermissionConst::label($permission) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center gap-1 text-primary" title="{{ __('admin/role.locked') }}">
                                            <i class="fa-solid fa-lock text-xs"></i>
                                        </span>
                                    </td>
                                    @foreach ($editableRoles as $role)
                                        <td class="px-4 py-3 text-center">
                                            @if ($locked)
                                                <span class="text-xs text-gray-400">{{ __('admin/role.super_admin_only') }}</span>
                                            @else
                                                <input type="checkbox"
                                                    id="permission-{{ $role }}-{{ str_replace('.', '-', $permission) }}"
                                                    name="permissions[{{ $role }}][]"
                                                    value="{{ $permission }}"
                                                    aria-label="{{ __('admin/role.grant_for', ['permission' => \App\Const\PermissionConst::label($permission), 'role' => $roles[$role] ?? $role]) }}"
                                                    class="h-4 w-4 border-border rounded accent-accent"
                                                    @checked(in_array($permission, $matrix[$role] ?? [], true))>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-5">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors">
                    <i class="fa-solid fa-floppy-disk"></i>
                    {{ __('admin/role.actions.save') }}
                </button>
            </div>
        </div>
    </form>
@endsection
