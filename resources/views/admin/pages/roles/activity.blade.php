@extends('admin.layouts.app')

@section('title', __('admin/role.activity.title'))

@section('content')
    <div class="w-full mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ __('admin/role.activity.title') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('admin/role.activity.subtitle') }}</p>
            </div>
            <a href="{{ route('admin.roles.index') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('admin/role.actions.back') }}
            </a>
        </div>

        <form action="{{ route('admin.roles.activity') }}" method="GET"
            class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end mt-5">
            <div>
                <label for="action" class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/role.activity.columns.action') }}</label>
                <select id="action" name="action" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-accent/30">
                    <option value="">{{ __('admin/role.activity.all_actions') }}</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action }}" @selected(($filters['action'] ?? null) === $action)>
                            {{ \App\Const\AdminActivityConst::actionLabel($action) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="self-end px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors">
                    <i class="fa-solid fa-filter mr-1"></i>{{ __('admin/role.activity.filter') }}
                </button>
                <a href="{{ route('admin.roles.activity') }}"
                    class="self-end px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fa-solid fa-rotate-left mr-1"></i>{{ __('admin/role.activity.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="w-full overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-[860px] w-full admin-table admin-table-sticky">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left uppercase bg-primary text-white">
                        <th class="w-[16%] px-4 py-3">{{ __('admin/role.activity.columns.time') }}</th>
                        <th class="w-[18%] px-4 py-3">{{ __('admin/role.activity.columns.admin') }}</th>
                        <th class="w-[18%] px-4 py-3">{{ __('admin/role.activity.columns.action') }}</th>
                        <th class="w-[16%] px-4 py-3">{{ __('admin/role.activity.columns.subject') }}</th>
                        <th class="w-[24%] px-4 py-3">{{ __('admin/role.activity.columns.details') }}</th>
                        <th class="w-[8%] px-4 py-3">{{ __('admin/role.activity.columns.ip') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($logs as $log)
                        <tr class="text-sm text-gray-700 align-top">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $log->admin?->name ?? __('admin/role.activity.deleted_admin') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-primary-soft text-primary">
                                    {{ \App\Const\AdminActivityConst::actionLabel($log->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $log->subject_type ? class_basename($log->subject_type) . ' #' . $log->subject_id : __('admin/role.activity.system') }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                @if (! empty($log->properties['changes']))
                                    <ul class="space-y-1">
                                        @foreach ($log->properties['changes'] as $role => $change)
                                            <li>
                                                <span class="font-medium text-gray-700">{{ \App\Const\AdminConst::roleLabel((int) $role) }}:</span>
                                                @if (! empty($change['granted']))
                                                    {{ __('admin/role.activity.granted') }}
                                                    {{ implode(', ', array_map(fn ($permission) => \App\Const\PermissionConst::label($permission), $change['granted'])) }}
                                                @endif
                                                @if (! empty($change['revoked']))
                                                    · {{ __('admin/role.activity.revoked') }}
                                                    {{ implode(', ', array_map(fn ($permission) => \App\Const\PermissionConst::label($permission), $change['revoked'])) }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @elseif (! empty($log->properties['changed']))
                                    <span class="font-medium text-gray-700">{{ __('admin/role.activity.changed_fields') }}:</span>
                                    {{ implode(', ', $log->properties['changed']) }}
                                @elseif (! empty($log->properties['email']))
                                    {{ $log->properties['email'] }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-400 whitespace-nowrap">{{ $log->ip ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center text-gray-500">
                                <i class="fa-solid fa-clock-rotate-left text-4xl text-gray-300 block mb-3"></i>
                                <p class="font-medium text-gray-700">{{ __('admin/role.activity.empty') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $logs])
    </div>
@endsection
