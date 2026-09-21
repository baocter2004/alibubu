<?php

use App\Http\Middleware\OverwriteAuthenticate;
use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\EnsureAdminIsActive;
use App\Http\Middleware\EnsureAdminCanWrite;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/web.php',
            __DIR__ . '/../routes/client.php',
            __DIR__ . '/../routes/admin.php',
        ],
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => OverwriteAuthenticate::class,
            'admin.role' => EnsureAdminRole::class,
            'admin.active' => EnsureAdminIsActive::class,
            'admin.can_write' => EnsureAdminCanWrite::class,
        ]);

        $middleware->web(append: [
            SetLocale::class,
            EnsureUserIsActive::class,
        ]);

        $middleware->append(SecurityHeaders::class);

        $middleware->trustHosts(at: function () {
            $host = parse_url((string) config('app.url'), PHP_URL_HOST);

            return $host ? [$host] : [];
        });

        $middleware->trustProxies(
            at: env('TRUSTED_PROXIES') === '*' ? '*' : array_filter(explode(',', (string) env('TRUSTED_PROXIES'))),
            headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO,
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
