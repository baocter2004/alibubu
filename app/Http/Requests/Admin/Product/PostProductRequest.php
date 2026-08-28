<?php

namespace App\Http\Requests\Admin\Product;

use App\Const\GlobalConst;
use App\Const\ProductConst;
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
        $this->merge([
            'type' => (int) $this->input('type', ProductConst::SINGLE),
            'variants' => array_values(array_filter(
                $this->input('variants', []),
                fn ($variant) => ! empty($variant['price']) || ! empty($variant['sku']) || ! empty($variant['attribute_value_ids'])
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

        return [
            'type' => ['required', Rule::in([ProductConst::SINGLE, ProductConst::VARIANT])],
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($id)],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($id)],
            'branch_id' => ['required', 'uuid', 'exists:branches,id'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['uuid', 'exists:categories,id'],
            'short_descriptions' => ['nullable', 'string', 'max:255'],
            'descriptions' => ['nullable', 'string', 'max:5000'],
            'thumbnail' => [$id ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'price' => [Rule::requiredIf(! $isVariable), 'nullable', 'numeric', 'min:0', 'max:99999999999'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'sale_price_start_at' => ['nullable', 'date', 'required_with:sale_price'],
            'sale_price_end_at' => ['nullable', 'date', 'after:sale_price_start_at'],

            'variants' => [Rule::requiredIf($isVariable), 'array', 'max:20'],
            'variants.*.id' => ['nullable', 'uuid', 'exists:product_variants,id'],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.price' => ['required_with:variants.*', 'nullable', 'numeric', 'min:0', 'max:99999999999'],
            'variants.*.sale_price' => ['nullable', 'numeric', 'min:0', 'lt:variants.*.price'],
            'variants.*.is_active' => ['nullable', 'boolean'],
            'variants.*.attribute_value_ids' => [Rule::requiredIf($isVariable), 'array', 'min:1'],
            'variants.*.attribute_value_ids.*' => ['uuid', 'exists:attribute_values,id'],
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
            'short_descriptions' => __('admin/product.fields.short_descriptions'),
            'descriptions' => __('admin/product.fields.descriptions'),
            'thumbnail' => __('admin/product.fields.thumbnail'),
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
            'variants.*.attribute_value_ids' => __('admin/product.fields.attributes'),
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

            foreach ($variants as $index => $variant) {
                $values = collect($variant['attribute_value_ids'] ?? [])->sort()->implode('-');

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
            }
        });
    }
}
