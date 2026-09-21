<?php

namespace App\Http\Middleware;

use App\Const\AdminConst;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $admin = Auth::guard('admin')->user();
        $allowedRoles = collect($roles)
            ->map(fn (string $role) => AdminConst::resolveRole(trim($role)))
            ->filter()
            ->values()
            ->all();

        abort_unless(
            $admin
                && $admin->isActive()
                && AdminConst::isValidRole($admin->role)
                && in_array((int) $admin->role, $allowedRoles, true),
            403,
            __('admin/auth.messages.forbidden')
        );

        return $next($request);
    }
}
