<?php

namespace App\Http\Middleware;

use App\Const\SecurityConst;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = [
            'X-Frame-Options' => SecurityConst::HEADER_FRAME_OPTIONS,
            'Content-Security-Policy' => SecurityConst::HEADER_CONTENT_SECURITY_POLICY,
            'X-Content-Type-Options' => SecurityConst::HEADER_CONTENT_TYPE_OPTIONS,
            'Referrer-Policy' => SecurityConst::HEADER_REFERRER_POLICY,
            'Permissions-Policy' => SecurityConst::HEADER_PERMISSIONS_POLICY,
        ];

        if ($request->isSecure()) {
            $headers['Strict-Transport-Security'] = SecurityConst::HEADER_HSTS;
        }

        foreach ($headers as $name => $value) {
            if (! $response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }

        return $response;
    }
}
