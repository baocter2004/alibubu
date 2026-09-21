<?php

namespace App\Providers;

use App\Const\SecurityConst;
use Closure;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class SecurityServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $client = $this->backWithError('client_auth.messages.throttled');
        $admin = $this->backWithError('admin/auth.messages.throttled');

        RateLimiter::for(SecurityConst::LIMITER_LOGIN, fn (Request $request) => [
            Limit::perMinute(SecurityConst::LOGIN_PER_EMAIL_PER_MINUTE)->by('email:' . $this->emailKey($request) . '|' . $request->ip())->response($client),
            Limit::perMinute(SecurityConst::LOGIN_PER_IP_PER_MINUTE)->by('ip:' . $request->ip())->response($client),
        ]);

        RateLimiter::for(SecurityConst::LIMITER_REGISTER, fn (Request $request) => [
            Limit::perMinute(SecurityConst::REGISTER_PER_IP_PER_MINUTE)->by('ip:' . $request->ip())->response($client),
            Limit::perHour(SecurityConst::REGISTER_PER_IP_PER_HOUR)->by('ip-hour:' . $request->ip())->response($client),
        ]);

        RateLimiter::for(SecurityConst::LIMITER_PASSWORD_EMAIL, fn (Request $request) => [
            Limit::perMinute(SecurityConst::PASSWORD_EMAIL_PER_EMAIL_PER_MINUTE)->by('email:' . $this->emailKey($request) . '|' . $request->ip())->response($client),
            Limit::perMinute(SecurityConst::PASSWORD_EMAIL_PER_IP_PER_MINUTE)->by('ip:' . $request->ip())->response($client),
        ]);

        RateLimiter::for(SecurityConst::LIMITER_PASSWORD_RESET, fn (Request $request) => [
            Limit::perMinute(SecurityConst::PASSWORD_RESET_PER_EMAIL_PER_MINUTE)->by('email:' . $this->emailKey($request) . '|' . $request->ip())->response($client),
            Limit::perMinute(SecurityConst::PASSWORD_RESET_PER_IP_PER_MINUTE)->by('ip:' . $request->ip())->response($client),
        ]);

        RateLimiter::for(SecurityConst::LIMITER_VERIFICATION, fn (Request $request) => [
            Limit::perMinute(SecurityConst::VERIFICATION_PER_MINUTE)->by('user:' . $this->userKey($request))->response($client),
            Limit::perHour(SecurityConst::VERIFICATION_PER_HOUR)->by('user-hour:' . $this->userKey($request))->response($client),
        ]);

        RateLimiter::for(SecurityConst::LIMITER_ADMIN_LOGIN, fn (Request $request) => [
            Limit::perMinute(SecurityConst::ADMIN_LOGIN_PER_EMAIL_PER_MINUTE)->by('email:' . $this->emailKey($request) . '|' . $request->ip())->response($admin),
            Limit::perMinute(SecurityConst::ADMIN_LOGIN_PER_IP_PER_MINUTE)->by('ip:' . $request->ip())->response($admin),
            Limit::perHour(SecurityConst::ADMIN_LOGIN_PER_ACCOUNT_PER_HOUR)->by('account:' . $this->emailKey($request))->response($admin),
        ]);

        RateLimiter::for(SecurityConst::LIMITER_ADMIN_PASSWORD, fn (Request $request) => [
            Limit::perMinute(SecurityConst::ADMIN_PASSWORD_PER_EMAIL_PER_MINUTE)->by('email:' . $this->emailKey($request) . '|' . $request->ip())->response($admin),
            Limit::perMinute(SecurityConst::ADMIN_PASSWORD_PER_IP_PER_MINUTE)->by('ip:' . $request->ip())->response($admin),
        ]);

        RateLimiter::for(SecurityConst::LIMITER_API, fn (Request $request) => Limit::perMinute(SecurityConst::API_PER_MINUTE)->by('ip:' . $request->ip()));
    }

    protected function emailKey(Request $request): string
    {
        return Str::lower(trim((string) $request->input('email')));
    }

    protected function userKey(Request $request): string
    {
        return (string) ($request->user()?->getAuthIdentifier() ?: $request->ip());
    }

    protected function backWithError(string $key): Closure
    {
        return function (Request $request, array $headers) use ($key) {
            $message = __($key, ['seconds' => (int) ($headers['Retry-After'] ?? 60)]);

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 429, $headers);
            }

            return back()
                ->withInput($request->except(SecurityConst::SENSITIVE_INPUTS))
                ->with('error', $message)
                ->withHeaders($headers);
        };
    }
}
