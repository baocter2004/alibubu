<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as MiddlewareAuthenticate;


class OverwriteAuthenticate extends MiddlewareAuthenticate
{
    protected array $guards = [];

    public function handle($request, \Closure $next, ...$guards)
    {
        $this->guards = $guards;

        return parent::handle($request, $next, ...$guards);
    }

    protected function redirectTo($request)
    {
        if ($request->expectsJson()) {
            throw new AuthenticationException();
        }

        $guard = $this->guards[0] ?? null;

        return match ($guard) {
            'admin' => route('auth.admin.showFormLogin'),
            default => route('auth.client.showFormLogin'),
        };
    }
}
