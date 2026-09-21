<?php

namespace App\Providers;

use App\Const\PermissionConst;
use App\Models\Admin;
use App\Services\Admin\RoleService;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(RoleService::class);
    }

    public function boot(): void
    {
        Gate::before(function ($user) {
            if (! $user instanceof Admin) {
                return null;
            }

            if (! $user->isActive()) {
                return Response::deny(__('admin/auth.messages.forbidden'));
            }

            return $user->isSuperAdmin() ? true : null;
        });

        foreach (PermissionConst::all() as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user instanceof Admin && $user->hasPermission($permission)
                    ? Response::allow()
                    : Response::deny(__('admin/auth.messages.forbidden'));
            });
        }
    }
}
