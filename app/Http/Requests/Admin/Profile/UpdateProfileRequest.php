<?php

namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore(Auth::guard('admin')->id()),
            ],
        ];

        if ($this->input('email') !== Auth::guard('admin')->user()?->email) {
            $rules['current_password'] = ['required', 'current_password:admin'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('admin/profile.fields.name'),
            'email' => __('admin/profile.fields.email'),
            'current_password' => __('admin/profile.fields.current_password'),
        ];
    }
}
