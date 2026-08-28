@php
    $isEdit = ! empty($product);
    $values = $isEdit ? $product->toArray() : ($data ?? []);
    $selectedCategories = $isEdit
        ? $product->categories->pluck('id')->all()
        : ($values['category_ids'] ?? []);
    $currentType = (int) old('type', $values['type'] ?? \App\Const\ProductConst::SINGLE);
    $existingVariants = $isEdit
        ? $product->variants->map(fn($variant) => [
            'id' => $variant->id,
            'sku' => $variant->sku,
            'price' => $variant->price,
            'sale_price' => $variant->sale_price,
            'is_active' => $variant->is_active,
            'attribute_value_ids' => $variant->attributeValues->pluck('id')->all(),
        ])->all()
        : ($values['variants'] ?? []);
    $variantRows = old('variants', $existingVariants);
@endphp

<form
    action="{{ $isEdit ? route('admin.products.confirm', $product->id) : route('admin.products.confirm') }}"
    method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf

    <section>
        <h2 class="text-base font-semibold text-gray-900 pb-2 mb-5 border-b border-gray-200">
            {{ __('admin/product.sections.general') }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @include('components.input', [
                'label' => __('admin/product.fields.name'),
                'name' => 'name',
                'required' => true,
                'icon' => 'box',
                'value' => $values['name'] ?? '',
            ])

            @include('components.select', [
                'label' => __('admin/product.fields.branch'),
                'name' => 'branch_id',
                'required' => true,
                'placeholder' => __('common.labels.all'),
                'options' => $branches->toArray(),
                'value' => $values['branch_id'] ?? '',
            ])

            @include('components.input', [
                'label' => __('admin/product.fields.sku'),
                'name' => 'sku',
                'icon' => 'barcode',
                'value' => $values['sku'] ?? '',
            ])
        </div>

        <div class="mt-5">
            <label class="flex items-center gap-x-2 text-sm font-medium text-blue-500 mb-2">
                <i class="fa-solid fa-layer-group"></i>
                {{ __('admin/product.fields.categories') }}
                <span class="text-red-500 text-base leading-none">*</span>
            </label>

            <div
                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-44 overflow-y-auto p-3 border rounded-lg {{ $errors->has('category_ids') ? 'is-invalid' : 'border-gray-300' }}">
                @foreach ($categories as $id => $name)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="category_ids[]" value="{{ $id }}"
                            @checked(in_array($id, old('category_ids', $selectedCategories), false))
                            class="h-4 w-4 rounded accent-blue-500">
                        <span class="text-sm text-gray-700 truncate">{{ $name }}</span>
                    </label>
                @endforeach
            </div>

            @error('category_ids')
                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-5">
            @include('components.input', [
                'label' => __('admin/product.fields.short_descriptions'),
                'name' => 'short_descriptions',
                'icon' => 'align-left',
                'value' => $values['short_descriptions'] ?? '',
            ])
        </div>

        <div class="mt-5">
            <label for="descriptions" class="flex items-center gap-x-2 text-sm font-medium text-blue-500 mb-2">
                <i class="fa-solid fa-file-lines"></i>
                {{ __('admin/product.fields.descriptions') }}
            </label>
            <textarea id="descriptions" name="descriptions" rows="5"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all {{ $errors->has('descriptions') ? 'is-invalid' : 'border-gray-300' }}">{{ old('descriptions', $values['descriptions'] ?? '') }}</textarea>
            @error('descriptions')
                <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
            @enderror
        </div>
    </section>

    <section>
        <h2 class="text-base font-semibold text-gray-900 pb-2 mb-5 border-b border-gray-200">
            {{ __('admin/product.fields.type') }}
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach ([\App\Const\ProductConst::SINGLE, \App\Const\ProductConst::VARIANT] as $type)
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="{{ $type }}" @checked($currentType === $type)
                        class="peer sr-only product-type-option">
                    <span
                        class="flex items-center gap-3 px-4 py-3 border-2 border-gray-200 rounded-lg transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50">
                        <i
                            class="fa-solid {{ $type === \App\Const\ProductConst::SINGLE ? 'fa-cube' : 'fa-cubes' }} text-blue-500"></i>
                        <span class="text-sm font-medium text-gray-800">{{ __('enum.product.type.' . $type) }}</span>
                    </span>
                </label>
            @endforeach
        </div>
        @error('type')
            <p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>
        @enderror
    </section>

    <section id="simple-pricing" class="{{ $currentType === \App\Const\ProductConst::VARIANT ? 'hidden' : '' }}">
        <h2 class="text-base font-semibold text-gray-900 pb-2 mb-5 border-b border-gray-200">
            {{ __('admin/product.sections.pricing') }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @include('components.input', [
                'label' => __('admin/product.fields.price'),
                'name' => 'price',
                'type' => 'number',
                'icon' => 'tag',
                'value' => $values['price'] ?? '',
            ])

            <div>
                @include('components.input', [
                    'label' => __('admin/product.fields.sale_price'),
                    'name' => 'sale_price',
                    'type' => 'number',
                    'icon' => 'percent',
                    'value' => $values['sale_price'] ?? '',
                ])
                <p class="text-xs text-gray-500 mt-1.5">{{ __('admin/product.hints.sale_price') }}</p>
            </div>

            @include('components.input', [
                'label' => __('admin/product.fields.sale_price_start_at'),
                'name' => 'sale_price_start_at',
                'type' => 'date',
                'icon' => 'calendar',
                'value' => isset($values['sale_price_start_at']) ? substr((string) $values['sale_price_start_at'], 0, 10) : '',
            ])

            @include('components.input', [
                'label' => __('admin/product.fields.sale_price_end_at'),
                'name' => 'sale_price_end_at',
                'type' => 'date',
                'icon' => 'calendar-check',
                'value' => isset($values['sale_price_end_at']) ? substr((string) $values['sale_price_end_at'], 0, 10) : '',
            ])
        </div>
    </section>

    <section id="variant-pricing" class="{{ $currentType === \App\Const\ProductConst::VARIANT ? '' : 'hidden' }}">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 mb-5 border-b border-gray-200">
            <div>
                <h2 class="text-base font-semibold text-gray-900">{{ __('admin/product.variant.section') }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('admin/product.variant.description') }}</p>
            </div>
            <button type="button" id="add-variant-btn"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                <i class="fa-solid fa-plus"></i>
                {{ __('admin/product.variant.add') }}
            </button>
        </div>

        @error('variants')
            <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
        @enderror

        <div id="variant-list" class="space-y-4">
            @foreach ($variantRows as $index => $variant)
                @include('admin.pages.products.variant-row', ['index' => $index, 'variant' => $variant])
            @endforeach
        </div>

        <p id="variant-empty" class="{{ count($variantRows) ? 'hidden' : '' }} py-8 text-center text-sm text-gray-500">
            {{ __('admin/product.variant.empty') }}
        </p>

        <template id="variant-template">
            @include('admin.pages.products.variant-row', ['index' => 'INDEX', 'variant' => []])
        </template>
    </section>

    <section>
        <h2 class="text-base font-semibold text-gray-900 pb-2 mb-5 border-b border-gray-200">
            {{ __('admin/product.sections.media') }}
        </h2>

        @include('components.input', [
            'label' => __('admin/product.fields.thumbnail'),
            'name' => 'thumbnail',
            'type' => 'file',
            'required' => ! $isEdit,
            'icon' => 'image',
            'value' => $values['thumbnail'] ?? '',
        ])
        <p class="text-xs text-gray-500 mt-1.5">{{ __('admin/product.hints.thumbnail') }}</p>
    </section>

    <section>
        <h2 class="text-base font-semibold text-gray-900 pb-2 mb-5 border-b border-gray-200">
            {{ __('admin/product.sections.visibility') }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start">
            @include('components.select', [
                'label' => __('admin/product.fields.is_active'),
                'name' => 'is_active',
                'required' => true,
                'options' => \App\Const\GlobalConst::statuses(),
                'value' => (string) ($values['is_active'] ?? \App\Const\GlobalConst::IS_ACTIVE),
            ])

            <label class="flex items-center gap-2 cursor-pointer md:mt-9">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1"
                    @checked(old('is_featured', $values['is_featured'] ?? false)) class="h-4 w-4 rounded accent-blue-500">
                <span class="text-sm text-gray-700">{{ __('admin/product.fields.is_featured') }}</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer md:mt-9">
                <input type="hidden" name="is_trending" value="0">
                <input type="checkbox" name="is_trending" value="1"
                    @checked(old('is_trending', $values['is_trending'] ?? false)) class="h-4 w-4 rounded accent-blue-500">
                <span class="text-sm text-gray-700">{{ __('admin/product.fields.is_trending') }}</span>
            </label>
        </div>
    </section>

    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
        <a href="{{ route('admin.products.index') }}"
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

@push('scripts')
    <script>
        $(function() {
            const VARIANT_LABEL = @json(__('admin/product.fields.variant_number', ['number' => ':n']));
            let variantIndex = {{ count($variantRows) }};

            function refreshVariantState() {
                const count = $('#variant-list .variant-item').length;
                $('#variant-empty').toggleClass('hidden', count > 0);

                $('#variant-list .variant-item').each(function(position) {
                    $(this).find('.variant-title').text(VARIANT_LABEL.replace(':n', position + 1));
                });
            }

            function toggleTypeSections() {
                const isVariable = $('.product-type-option:checked').val() === '{{ \App\Const\ProductConst::VARIANT }}';
                $('#variant-pricing').toggleClass('hidden', !isVariable);
                $('#simple-pricing').toggleClass('hidden', isVariable);

                if (isVariable && $('#variant-list .variant-item').length === 0) {
                    $('#add-variant-btn').trigger('click');
                }
            }

            $('#add-variant-btn').on('click', function() {
                const markup = $('#variant-template').html().replace(/INDEX/g, variantIndex);
                $('#variant-list').append(markup);
                variantIndex += 1;
                refreshVariantState();
            });

            $(document).on('click', '.remove-variant-btn', function() {
                $(this).closest('.variant-item').remove();
                refreshVariantState();
            });

            $('.product-type-option').on('change', toggleTypeSections);

            refreshVariantState();
        });
    </script>
@endpush
