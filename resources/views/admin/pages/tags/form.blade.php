@php $values = ! empty($tag) ? $tag->toArray() : ($data ?? []); @endphp

<form action="{{ $formAction }}" method="POST" class="space-y-6">
    @csrf

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
        <div class="max-w-md">
            @include('components.input', [
                'label' => __('admin/tag.fields.name'),
                'name' => 'name',
                'required' => true,
                'icon' => 'tag',
                'value' => $values['name'] ?? '',
            ])
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('admin.tags.index') }}"
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
