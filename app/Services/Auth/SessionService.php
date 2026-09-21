<?php

namespace App\Services\Auth;

use App\Const\SecurityConst;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SessionService
{
    public function login(Request $request, string $guard, Authenticatable $user, bool $remember = false): void
    {
        Auth::guard($guard)->login($user, $remember);

        $this->regenerate($request, $guard);
    }

    public function regenerate(Request $request, string $guard): void
    {
        $request->session()->regenerate();
        $request->session()->forget(SecurityConst::SESSION_PASSWORD_HASH_PREFIX . $guard);
    }

    public function logout(Request $request, string $guard): void
    {
        Auth::guard($guard)->logout();

        $session = $request->session();
        $session->forget(SecurityConst::SESSION_PASSWORD_HASH_PREFIX . $guard);

        $otherGuardActive = collect(array_keys(config('auth.guards', [])))
            ->reject(fn ($name) => in_array($name, [$guard, 'web', 'sanctum'], true))
            ->contains(fn ($name) => Auth::guard($name)->check());

        if ($otherGuardActive) {
            $session->migrate(true);
        } else {
            $session->invalidate();
        }

        $session->regenerateToken();
    }

    public function terminateOtherSessions(Authenticatable $user, ?string $exceptSessionId = null): void
    {
        try {
            if ($user->getRememberTokenName()) {
                $user->setRememberToken(Str::random(60));
                $user->save();
            }

            if (config('session.driver') !== 'database') {
                return;
            }

            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $user->getAuthIdentifier())
                ->when($exceptSessionId, fn ($query) => $query->where('id', '!=', $exceptSessionId))
                ->delete();
        } catch (\Throwable $th) {
            Log::error(__METHOD__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }
}
