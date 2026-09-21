<?php

namespace App\Http\Requests\User;

use App\Const\PasswordConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'confirmed', PasswordConst::customer()],
        ];
    }

    public function attributes(): array
    {
        return [
            'fullname' => __('client_auth.register.fullname'),
            'email' => __('client_auth.register.email'),
            'password' => __('client_auth.register.password'),
        ];
    }
}
