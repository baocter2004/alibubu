<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminCanWrite
{
    public function handle(Request $request, Closure $next, string $createAbility, string $updateAbility, string $sessionKey): Response
    {
        $route = $request->route();

        $id = $route && in_array('id', $route->parameterNames(), true)
            ? $route->parameter('id')
            : data_get($request->session()->get($sessionKey), 'id');

        Gate::authorize(filled($id) ? $updateAbility : $createAbility);

        return $next($request);
    }
}
