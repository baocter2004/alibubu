<?php

namespace App\Http\Middleware;

use App\Const\AdminConst;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $admin = Auth::guard('admin')->user();
        $allowedRoles = collect($roles)
            ->map(fn ($role) => is_numeric($role) ? (int) $role : array_search($role, AdminConst::roles(), true))
            ->filter(fn ($role) => $role !== false && in_array($role, AdminConst::allRoleIds(), true))
            ->values()
            ->all();

        abort_unless($admin && in_array((int) $admin->role, $allowedRoles, true), 403, __('admin/auth.forbidden'));

        return $next($request);
    }
}
