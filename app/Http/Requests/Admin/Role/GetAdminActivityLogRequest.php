<?php

namespace App\Http\Requests\Admin\Role;

use App\Const\PermissionConst;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class GetAdminActivityLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows(PermissionConst::ROLES_MANAGE);
    }

    public function rules(): array
    {
        return [
            'action' => ['nullable', 'string', 'max:100'],
            'admin_id' => ['nullable', 'uuid'],
        ];
    }
}
