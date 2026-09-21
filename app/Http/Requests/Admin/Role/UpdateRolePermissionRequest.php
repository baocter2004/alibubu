<?php

namespace App\Http\Requests\Admin\Role;

use App\Const\AdminConst;
use App\Const\PermissionConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateRolePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows(PermissionConst::ROLES_MANAGE);
    }

    public function rules(): array
    {
        $grantable = array_values(array_diff(PermissionConst::all(), PermissionConst::superAdminOnly()));

        return [
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['array'],
            'permissions.*.*' => ['string', Rule::in($grantable)],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                foreach (array_keys((array) $this->input('permissions', [])) as $role) {
                    if (! in_array((int) $role, AdminConst::editableRoleIds(), true)) {
                        $validator->errors()->add('permissions', __('admin/role.messages.invalid_role'));
                    }
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'permissions' => __('admin/role.fields.permissions'),
            'permissions.*' => __('admin/role.fields.permissions'),
            'permissions.*.*' => __('admin/role.fields.permission'),
        ];
    }

    public function permissionsByRole(): array
    {
        $input = (array) $this->validated('permissions', []);

        return collect(AdminConst::editableRoleIds())
            ->mapWithKeys(fn (int $role) => [$role => array_values((array) ($input[$role] ?? []))])
            ->all();
    }
}
