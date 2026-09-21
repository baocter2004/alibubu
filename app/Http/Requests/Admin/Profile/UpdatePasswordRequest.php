<?php

namespace App\Http\Requests\Admin\Profile;

use App\Const\PasswordConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'string', 'confirmed', 'different:current_password', PasswordConst::admin()],
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => __('admin/profile.fields.current_password'),
            'password' => __('admin/profile.fields.new_password'),
        ];
    }
}
