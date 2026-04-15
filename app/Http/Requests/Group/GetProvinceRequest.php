<?php

namespace App\Http\Requests\Group;

use App\Const\ProvinceConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetProvinceRequest extends FormRequest
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
            'keyword' => "nullable|string|max:255",
            'name' => "nullable|string|max:255",
            'division_type' => [
                'nullable',
                Rule::in(ProvinceConst::DIVISION_TYPE)
            ]
        ];
    }
}
