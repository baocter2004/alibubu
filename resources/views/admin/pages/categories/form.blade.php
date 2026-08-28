@php
    $isEdit = ! empty($category);
    $values = $isEdit ? $category->toArray() : ($data ?? []);
@endphp

<form action="{{ $isEdit ? route('admin.categories.confirm', $category->id) : route('admin.categories.confirm') }}"
    method="POST" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @include('components.input', [
            'label' => __('admin/category.fields.name'),
            'name' => 'name',
            'required' => true,
            'icon' => 'tag',
            'value' => $values['name'] ?? '',
        ])
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            @include('components.select', [
                'label' => __('admin/category.fields.parent'),
                'name' => 'parent_id',
                'placeholder' => __('admin/category.no_parent'),
                'options' => $parents->toArray(),
                'value' => $values['parent_id'] ?? '',
            ])
            <p class="text-xs text-gray-500 mt-1.5">{{ __('admin/category.hints.parent') }}</p>
        </div>

        <div>
            @include('components.input', [
                'label' => __('admin/category.fields.icon'),
                'name' => 'icon',
                'icon' => 'icons',
                'placeholder' => 'fa-solid fa-mobile-screen',
                'value' => $values['icon'] ?? '',
            ])
            <p class="text-xs text-gray-500 mt-1.5">{{ __('admin/category.hints.icon') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @include('components.input', [
            'label' => __('admin/category.fields.ordinal'),
            'name' => 'ordinal',
            'type' => 'number',
            'icon' => 'sort',
            'value' => $values['ordinal'] ?? 0,
        ])

        @include('components.select', [
            'label' => __('admin/category.fields.is_active'),
            'name' => 'is_active',
            'required' => true,
            'options' => \App\Const\GlobalConst::statuses(),
            'value' => (string) ($values['is_active'] ?? \App\Const\GlobalConst::IS_ACTIVE),
        ])
    </div>

    <div class="flex justify-end gap-3 pt-2 border-t border-gray-200">
        <a href="{{ route('admin.categories.index') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            {{ __('common.actions.cancel') }}
        </a>
        <button type="submit"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
            <i class="fa-solid fa-arrow-right"></i>
            {{ __('common.actions.confirm') }}
        </button>
    </div>
</form>
