<?php

namespace App\Http\Requests\User;

use App\Const\PasswordConst;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'confirmed', PasswordConst::customer()],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'email',
            'password' => 'mật khẩu',
        ];
    }
}
