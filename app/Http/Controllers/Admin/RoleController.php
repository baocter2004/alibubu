<?php

namespace App\Http\Controllers\Admin;

use App\Const\AdminConst;
use App\Const\PermissionConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\GetAdminActivityLogRequest;
use App\Http\Requests\Admin\Role\UpdateRolePermissionRequest;
use App\Services\Admin\AdminActivityLogService;
use App\Services\Admin\RoleService;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService,
        protected AdminActivityLogService $activityLogService,
    ) {}

    public function index()
    {
        return view('admin.pages.roles.index', [
            'groups' => PermissionConst::groups(),
            'matrix' => $this->roleService->matrix(),
            'defaults' => PermissionConst::defaults(),
            'roles' => AdminConst::roles(),
            'editableRoles' => AdminConst::editableRoleIds(),
            'lockedPermissions' => PermissionConst::superAdminOnly(),
            'adminCounts' => $this->roleService->adminCountsByRole(),
            'usingDefaults' => $this->roleService->isUsingDefaults(),
        ]);
    }

    public function update(UpdateRolePermissionRequest $request)
    {
        $this->roleService->update($request->permissionsByRole());

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('admin/role.messages.updated'));
    }

    public function reset()
    {
        $this->roleService->resetToDefaults();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('admin/role.messages.reset'));
    }

    public function activity(GetAdminActivityLogRequest $request)
    {
        return view('admin.pages.roles.activity', [
            'logs' => $this->activityLogService->paginate($request->validated()),
            'actions' => $this->activityLogService->actions(),
            'filters' => $request->validated(),
        ]);
    }
}
