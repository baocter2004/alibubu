<?php

namespace App\Http\Requests\Client;

use App\Const\UserConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => ['required', 'string', 'max:255'],
            'phone_number' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^0[0-9]{8,10}$/',
                Rule::unique('users', 'phone_number')->ignore(Auth::id()),
            ],
            'gender' => ['nullable', Rule::in(array_keys(UserConst::genders()))],
            'birthday' => ['nullable', 'date', 'before:today'],
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
            'fullname' => __('client.account.fields.fullname'),
            'phone_number' => __('client.account.fields.phone_number'),
            'gender' => __('client.account.fields.gender'),
            'birthday' => __('client.account.fields.birthday'),
        ];
    }
}
