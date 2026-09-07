<?php

namespace App\Http\Requests\Admin\Product;

use App\Const\GlobalConst;
use App\Const\ProductConst;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $variants = array_map(function ($variant) {
            if (isset($variant['attribute_value_ids']) && is_array($variant['attribute_value_ids'])) {
                $variant['attribute_value_ids'] = array_values(array_filter(
                    $variant['attribute_value_ids'],
                    fn ($value) => is_string($value) && trim($value) !== ''
                ));
            }

            return $variant;
        }, $this->input('variants', []));

        $this->merge([
            'type' => (int) $this->input('type', ProductConst::SINGLE),
            'accessory_ids' => array_values(array_filter((array) $this->input('accessory_ids', []))),
            'variants' => array_values(array_filter(
                $variants,
                fn ($variant) => ! empty($variant['price']) || ! empty($variant['sku']) || ! empty($variant['attribute_value_ids'])
            )),
            'specifications' => array_values(array_filter(
                $this->input('specifications', []),
                fn ($spec) => ! empty($spec['name']) || ! empty($spec['value'])
            )),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');
        $isVariable = (int) $this->input('type') === ProductConst::VARIANT;
        $variantIdRule = Rule::exists('product_variants', 'id');
        if ($id) {
            $variantIdRule->where(fn ($query) => $query->where('product_id', $id));
        }

        return [
            'type' => ['required', Rule::in([ProductConst::SINGLE, ProductConst::VARIANT])],
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($id)],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($id)],
            'branch_id' => ['required', 'uuid', Rule::exists('branches', 'id')->where('is_active', true)],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['uuid', Rule::exists('categories', 'id')->where('is_active', true)],
            'accessory_ids' => ['nullable', 'array', 'max:12'],
            'accessory_ids.*' => ['uuid', Rule::exists('products', 'id')],
            'short_descriptions' => ['nullable', 'string', 'max:255'],
            'descriptions' => ['nullable', 'string', 'max:5000'],
            'thumbnail' => [$id ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'stock' => [Rule::requiredIf(! $isVariable), 'nullable', 'integer', 'min:0', 'max:4294967295'],
            'price' => [Rule::requiredIf(! $isVariable), 'nullable', 'numeric', 'min:0', 'max:99999999999'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'sale_price_start_at' => ['nullable', 'date', 'required_with:sale_price'],
            'sale_price_end_at' => ['nullable', 'date', 'after:sale_price_start_at'],

            'variants' => $isVariable
                ? ['required', 'array', 'min:1', 'max:20']
                : ['nullable', 'array', 'max:20'],
            'variants.*.id' => ['nullable', 'uuid', $variantIdRule],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.price' => ['required_with:variants.*', 'nullable', 'numeric', 'min:0', 'max:99999999999'],
            'variants.*.sale_price' => ['nullable', 'numeric', 'min:0', 'lt:variants.*.price'],
            'variants.*.stock' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'variants.*.is_active' => ['nullable', 'boolean'],
            'variants.*.attribute_value_ids' => [Rule::requiredIf($isVariable), 'array', 'min:1'],
            'variants.*.attribute_value_ids.*' => ['uuid', Rule::exists('attribute_values', 'id')->where('is_active', true)],

            'specifications' => ['nullable', 'array', 'max:40'],
            'specifications.*.id' => ['nullable', 'uuid', 'exists:product_specifications,id'],
            'specifications.*.group' => ['nullable', 'string', 'max:100'],
            'specifications.*.name' => ['required_with:specifications.*.value', 'nullable', 'string', 'max:120'],
            'specifications.*.value' => ['required_with:specifications.*.name', 'nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'is_trending' => ['nullable', 'boolean'],
            'is_active' => ['required', Rule::in(array_keys(GlobalConst::statuses()))],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('admin/product.fields.name'),
            'sku' => __('admin/product.fields.sku'),
            'branch_id' => __('admin/product.fields.branch'),
            'category_ids' => __('admin/product.fields.categories'),
            'accessory_ids' => __('admin/product.fields.accessories'),
            'short_descriptions' => __('admin/product.fields.short_descriptions'),
            'descriptions' => __('admin/product.fields.descriptions'),
            'thumbnail' => __('admin/product.fields.thumbnail'),
            'stock' => __('admin/product.fields.stock'),
            'price' => __('admin/product.fields.price'),
            'sale_price' => __('admin/product.fields.sale_price'),
            'sale_price_start_at' => __('admin/product.fields.sale_price_start_at'),
            'sale_price_end_at' => __('admin/product.fields.sale_price_end_at'),
            'is_active' => __('admin/product.fields.is_active'),
            'type' => __('admin/product.fields.type'),
            'variants' => __('admin/product.fields.variants'),
            'variants.*.sku' => __('admin/product.fields.sku'),
            'variants.*.price' => __('admin/product.fields.price'),
            'variants.*.sale_price' => __('admin/product.fields.sale_price'),
            'variants.*.stock' => __('admin/product.fields.stock'),
            'variants.*.attribute_value_ids' => __('admin/product.fields.attributes'),
            'specifications' => __('admin/product.fields.specifications'),
            'specifications.*.group' => __('admin/product.spec.group'),
            'specifications.*.name' => __('admin/product.spec.name'),
            'specifications.*.value' => __('admin/product.spec.value'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $variants = collect($this->input('variants', []));
            $signatures = [];
            $attributeValueIds = $variants
                ->flatMap(fn ($variant) => $variant['attribute_value_ids'] ?? [])
                ->filter()
                ->unique()
                ->values();
            $attributeValues = AttributeValue::query()
                ->with('attribute')
                ->whereIn('id', $attributeValueIds)
                ->get()
                ->keyBy('id');
            $variantSkus = [];

            foreach ($variants as $index => $variant) {
                $rawValues = collect($variant['attribute_value_ids'] ?? [])->filter()->values();
                $values = $rawValues->sort()->implode('-');

                if ($rawValues->count() !== $rawValues->unique()->count()) {
                    $validator->errors()->add(
                        "variants.$index.attribute_value_ids",
                        __('admin/product.messages.duplicate_attribute_value')
                    );
                }

                $attributeGroups = $rawValues
                    ->map(fn ($valueId) => $attributeValues->get($valueId)?->attribute_id)
                    ->filter();
                if ($attributeGroups->count() !== $attributeGroups->unique()->count()) {
                    $validator->errors()->add(
                        "variants.$index.attribute_value_ids",
                        __('admin/product.messages.one_value_per_attribute')
                    );
                }

                if ($values === '') {
                    continue;
                }

                if (in_array($values, $signatures, true)) {
                    $validator->errors()->add(
                        "variants.$index.attribute_value_ids",
                        __('admin/product.messages.duplicate_variant')
                    );
                }

                $signatures[] = $values;

                $sku = trim((string) ($variant['sku'] ?? ''));
                if ($sku !== '') {
                    $skuKey = mb_strtolower($sku);
                    if (isset($variantSkus[$skuKey])) {
                        $validator->errors()->add(
                            "variants.$index.sku",
                            __('admin/product.messages.duplicate_variant_sku')
                        );
                    }
                    $variantSkus[$skuKey] = $variant['id'] ?? null;
                }
            }

            if ($variantSkus !== []) {
                $existing = ProductVariant::query()
                    ->whereIn('sku', array_keys($variantSkus))
                    ->get(['sku', 'id']);
                foreach ($existing as $model) {
                    $submittedId = $variantSkus[mb_strtolower($model->sku)] ?? null;
                    if ((string) $submittedId !== (string) $model->id) {
                        $validator->errors()->add(
                            'variants',
                            __('admin/product.messages.variant_sku_exists', ['sku' => $model->sku])
                        );
                    }
                }
            }
        });
    }
}
