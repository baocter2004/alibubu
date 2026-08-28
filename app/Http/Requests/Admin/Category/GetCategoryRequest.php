<?php

namespace App\Http\Requests\Admin\Category;

use App\Const\GlobalConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetCategoryRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'uuid', 'exists:categories,id'],
            'is_active' => ['nullable', Rule::in(array_keys(GlobalConst::statuses()))],
        ];
    }
}
