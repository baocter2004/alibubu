@php
    $values = ! empty($attribute) ? $attribute->toArray() : ($data ?? []);
    $existingValues = ! empty($attribute)
        ? $attribute->values->map(fn($v) => ['id' => $v->id, 'value' => $v->value, 'is_active' => $v->is_active])->all()
        : ($values['values'] ?? []);
    $valueRows = old('values', $existingValues);
@endphp

<form action="{{ $formAction }}" method="POST" class="space-y-6">
    @csrf

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="px-5 py-4 border-b border-gray-100 bg-gray-50/60">
            <h2 class="font-semibold text-gray-900">{{ __('admin/attribute.title.show') }}</h2>
        </header>

        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-5">
            @include('components.input', [
                'label' => __('admin/attribute.fields.name'),
                'name' => 'name',
                'required' => true,
                'icon' => 'tag',
                'value' => $values['name'] ?? '',
            ])

            @include('components.select', [
                'label' => __('admin/attribute.fields.is_active'),
                'name' => 'is_active',
                'required' => true,
                'options' => \App\Const\GlobalConst::statuses(),
                'value' => (string) ($values['is_active'] ?? \App\Const\GlobalConst::IS_ACTIVE),
            ])
        </div>
    </section>

    <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100 bg-gray-50/60">
            <div>
                <h2 class="font-semibold text-gray-900">{{ __('admin/attribute.value_section.title') }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('admin/attribute.hints.values') }}</p>
            </div>

            <button type="button" id="add-value-btn"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors">
                <i class="fa-solid fa-plus"></i>
                {{ __('admin/attribute.value_section.add') }}
            </button>
        </header>

        <div class="p-5">
            @error('values')
                <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
            @enderror

            <div id="value-list" class="space-y-3">
                @foreach ($valueRows as $index => $value)
                    @include('admin.pages.attributes.value-row', ['index' => $index, 'value' => $value])
                @endforeach
            </div>

            <p id="value-empty" class="{{ count($valueRows) ? 'hidden' : '' }} py-8 text-center text-sm text-gray-500">
                {{ __('admin/attribute.value_section.empty') }}
            </p>

            <template id="value-template">
                @include('admin.pages.attributes.value-row', ['index' => 'INDEX', 'value' => []])
            </template>
        </div>
    </section>

    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('admin.attributes.index') }}"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            {{ __('common.actions.back') }}
        </a>
        <button type="submit"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary-hover transition-colors">
            <i class="fa-solid fa-arrow-right"></i>
            {{ __('common.actions.confirm') }}
        </button>
    </div>
</form>

@push('scripts')
    <script>
        $(function() {
            let valueIndex = {{ count($valueRows) }};

            function refresh() {
                $('#value-empty').toggleClass('hidden', $('#value-list .value-item').length > 0);
            }

            $('#add-value-btn').on('click', function() {
                $('#value-list').append($('#value-template').html().replace(/INDEX/g, valueIndex));
                valueIndex += 1;
                refresh();
            });

            $(document).on('click', '.remove-value-btn', function() {
                $(this).closest('.value-item').remove();
                refresh();
            });

            if (valueIndex === 0) {
                $('#add-value-btn').trigger('click');
            }

            refresh();
        });
    </script>
@endpush
