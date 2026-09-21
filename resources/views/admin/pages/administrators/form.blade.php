@php
    $isEdit = ! empty($administrator);
    $isSelf = $isEdit && (string) $administrator->id === (string) Auth::guard('admin')->id();
    $statusOptions = [
        \App\Const\GlobalConst::IS_ACTIVE => __('admin/administrator.status.active'),
        \App\Const\GlobalConst::IS_NOT_ACTIVE => __('admin/administrator.status.inactive'),
    ];
@endphp

<form action="{{ $isEdit ? route('admin.administrators.update', $administrator->id) : route('admin.administrators.store') }}" method="POST" class="space-y-6">
    @csrf
    @if ($isEdit)
        @method('PATCH')
    @endif

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @include('components.input', [
                'label' => __('admin/administrator.fields.name'),
                'name' => 'name',
                'required' => true,
                'icon' => 'user',
                'value' => $administrator->name ?? '',
            ])

            @include('components.input', [
                'label' => __('admin/administrator.fields.email'),
                'name' => 'email',
                'type' => 'email',
                'required' => true,
                'icon' => 'envelope',
                'value' => $administrator->email ?? '',
            ])

            @if ($isSelf)
                <div class="w-full">
                    <p class="flex items-center gap-x-2 text-sm font-semibold text-primary mb-2">
                        <i class="fa-solid fa-user-shield"></i>
                        {{ __('admin/administrator.fields.role') }}
                    </p>
                    <p class="px-4 py-2 rounded-lg bg-gray-50 border border-border text-gray-700">{{ $administrator->roleLabel() }}</p>
                    <input type="hidden" name="role" value="{{ $administrator->role }}">
                </div>

                <div class="w-full">
                    <p class="flex items-center gap-x-2 text-sm font-semibold text-primary mb-2">
                        <i class="fa-solid fa-toggle-on"></i>
                        {{ __('admin/administrator.fields.is_active') }}
                    </p>
                    <p class="px-4 py-2 rounded-lg bg-gray-50 border border-border text-gray-700">{{ __('admin/administrator.status.active') }}</p>
                    <input type="hidden" name="is_active" value="{{ \App\Const\GlobalConst::IS_ACTIVE }}">
                </div>

                <p class="md:col-span-2 text-xs text-gray-500 -mt-2">
                    <i class="fa-solid fa-lock mr-1"></i>{{ __('admin/administrator.hints.self_locked') }}
                </p>
            @else
                @include('components.select', [
                    'label' => __('admin/administrator.fields.role'),
                    'name' => 'role',
                    'required' => true,
                    'icon' => 'user-shield',
                    'options' => $roles,
                    'value' => (string) ($administrator->role ?? \App\Const\AdminConst::ROLE_STAFF),
                ])

                <div>
                    @include('components.select', [
                        'label' => __('admin/administrator.fields.is_active'),
                        'name' => 'is_active',
                        'required' => true,
                        'icon' => 'toggle-on',
                        'options' => $statusOptions,
                        'value' => (string) (int) ($administrator?->isActive() ?? true),
                    ])
                    <p class="text-xs text-gray-500 mt-1.5">{{ __('admin/administrator.hints.is_active') }}</p>
                </div>

                @unless (Auth::guard('admin')->user()?->isSuperAdmin())
                    <p class="md:col-span-2 text-xs text-gray-500 -mt-2">
                        <i class="fa-solid fa-circle-info mr-1"></i>{{ __('admin/administrator.hints.staff_only') }}
                    </p>
                @endunless
            @endif
        </div>
    </section>

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @include('components.input', [
                'label' => __('admin/administrator.fields.password'),
                'name' => 'password',
                'type' => 'password',
                'required' => ! $isEdit,
                'icon' => 'lock',
            ])

            @include('components.input', [
                'label' => __('admin/administrator.fields.password_confirmation'),
                'name' => 'password_confirmation',
                'type' => 'password',
                'required' => ! $isEdit,
                'icon' => 'shield-halved',
            ])
        </div>
        <p class="text-xs text-gray-500 mt-4">{{ __('admin/administrator.hints.' . ($isEdit ? 'password_edit' : 'password_create')) }}</p>
    </section>

    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('admin.administrators.index') }}"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            {{ __('common.actions.back') }}
        </a>
        <button type="submit"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors">
            <i class="fa-solid fa-floppy-disk"></i>
            {{ $isEdit ? __('common.actions.update') : __('common.actions.save') }}
        </button>
    </div>
</form>
