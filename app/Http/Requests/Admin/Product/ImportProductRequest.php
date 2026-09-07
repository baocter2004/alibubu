<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                Rule::requiredIf(! $this->filled('token')),
                'file',
                'mimes:xlsx,csv,txt',
                'extensions:xlsx,csv,txt',
                'max:10240',
            ],
            'token' => ['nullable', 'uuid'],
        ];
    }

    public function attributes(): array
    {
        return [
            'file' => __('admin/product.import.file'),
        ];
    }
}
