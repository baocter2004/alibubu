<?php

namespace App\Http\Requests\Admin\Administrator;

use App\Const\AdminConst;
use App\Const\PasswordConst;
use App\Const\PermissionConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PostAdministratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows(PermissionConst::ADMINISTRATORS_MANAGE);
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($id)],
            'role' => ['required', 'integer', Rule::in(AdminConst::allRoleIds())],
            'is_active' => ['required', 'boolean'],
            'password' => [$id ? 'nullable' : 'required', 'string', PasswordConst::admin(), 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('admin/administrator.fields.name'),
            'email' => __('admin/administrator.fields.email'),
            'role' => __('admin/administrator.fields.role'),
            'is_active' => __('admin/administrator.fields.is_active'),
            'password' => __('admin/administrator.fields.password'),
        ];
    }
}
