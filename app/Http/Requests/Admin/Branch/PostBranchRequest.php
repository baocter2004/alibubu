<?php

namespace App\Http\Requests\Admin\Branch;

use App\Const\GlobalConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostBranchRequest extends FormRequest
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
        $id = $this->route()->id;
        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('branches','slug')->ignore($id)
            ],
            'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => [
                'nullable',
                Rule::in(array_keys(GlobalConst::STATUS))
            ]
        ];
    }
}
