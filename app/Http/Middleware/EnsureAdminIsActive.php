<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin || $admin->isActive()) {
            return $next($request);
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $message = __('admin/administrator.messages.account_inactive');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 401);
        }

        return redirect()
            ->route('auth.admin.showFormLogin')
            ->with('error', $message);
    }
}
