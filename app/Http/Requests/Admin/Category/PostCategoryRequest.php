<?php

namespace App\Http\Requests\Admin\Category;

use App\Const\GlobalConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($id)],
            'icon' => ['nullable', 'string', 'max:100'],
            'parent_id' => ['nullable', 'uuid', 'exists:categories,id', Rule::notIn(array_filter([$id]))],
            'ordinal' => ['nullable', 'integer', 'min:0', 'max:9999'],
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
            'name' => __('admin/category.fields.name'),
            'icon' => __('admin/category.fields.icon'),
            'parent_id' => __('admin/category.fields.parent'),
            'ordinal' => __('admin/category.fields.ordinal'),
            'is_active' => __('admin/category.fields.is_active'),
        ];
    }
}
