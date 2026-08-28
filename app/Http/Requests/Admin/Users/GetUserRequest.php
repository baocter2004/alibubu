<?php

namespace App\Http\Requests\Admin\Users;

use App\Const\UserConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetUserRequest extends FormRequest
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
            'keyword' => 'nullable|string|max:255',
            'fullname' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'status' => [
                'nullable',
                'integer',
                Rule::in(array_keys(UserConst::statuses()))
            ],
            'role' => [
                'nullable',
                'integer',
                Rule::in(array_keys(UserConst::roles()))
            ],
        ];
    }
}
