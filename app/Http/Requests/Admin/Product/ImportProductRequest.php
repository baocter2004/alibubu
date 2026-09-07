<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class ImportProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,csv,txt', 'extensions:xlsx,csv,txt', 'max:10240'],
        ];
    }

    public function attributes(): array
    {
        return [
            'file' => __('admin/product.import.file'),
        ];
    }
}
