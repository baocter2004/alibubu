@php
    $values = ! empty($branch) ? $branch->toArray() : ($data ?? []);
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6 space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @include('components.input', [
                'label' => __('admin/branch.fields.name'),
                'name' => 'name',
                'required' => true,
                'icon' => 'store',
                'value' => $values['name'] ?? '',
            ])

            <div>
                @include('components.input', [
                    'label' => __('admin/branch.fields.slug'),
                    'name' => 'slug',
                    'icon' => 'link',
                    'value' => $values['slug'] ?? '',
                ])
                <p class="text-xs text-gray-500 mt-1.5">{{ __('admin/branch.hints.slug') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @include('components.select', [
                'label' => __('admin/branch.fields.is_active'),
                'name' => 'is_active',
                'required' => true,
                'options' => \App\Const\GlobalConst::statuses(),
                'value' => (string) ($values['is_active'] ?? \App\Const\GlobalConst::IS_ACTIVE),
            ])
        </div>

        <div>
            @include('components.input', [
                'label' => __('admin/branch.fields.logo'),
                'name' => 'logo',
                'type' => 'file',
                'required' => empty($branch),
                'icon' => 'image',
                'value' => $values['logo'] ?? '',
            ])
            <p class="text-xs text-gray-500 mt-1.5">{{ __('admin/branch.hints.logo') }}</p>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('admin.branches.index') }}"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            {{ __('common.actions.back') }}
        </a>
        <button type="submit"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
            <i class="fa-solid fa-arrow-right"></i>
            {{ __('common.actions.confirm') }}
        </button>
    </div>
</form>
