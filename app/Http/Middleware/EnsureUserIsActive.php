<?php

namespace App\Http\Middleware;

use App\Services\Auth\AuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function __construct(protected AuthService $authService) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin', 'admin/*')) {
            return $next($request);
        }

        $user = Auth::guard('user')->user();

        if (! $user || $user->isActive()) {
            return $next($request);
        }

        $message = $user->inactiveMessage();

        $this->authService->logout($request);

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()
            ->route('auth.client.showFormLogin')
            ->with('error', $message);
    }
}
