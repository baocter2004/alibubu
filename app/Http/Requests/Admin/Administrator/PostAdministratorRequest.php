<?php

namespace App\Http\Requests\Admin\Administrator;

use App\Const\AdminConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PostAdministratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($id)],
            'role' => ['required', 'integer', Rule::in(AdminConst::allRoleIds())],
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
