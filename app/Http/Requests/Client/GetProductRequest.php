<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetProductRequest extends FormRequest
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
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'is_sale' => ['nullable', 'boolean'],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'price_asc', 'price_desc', 'popular'])],
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
            'keyword' => 'từ khoá',
            'category_id' => 'danh mục',
            'branch_id' => 'thương hiệu',
            'min_price' => 'giá thấp nhất',
            'max_price' => 'giá cao nhất',
        ];
    }
}
