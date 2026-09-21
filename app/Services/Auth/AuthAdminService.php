<?php

namespace App\Services\Auth;

use App\Const\SecurityConst;
use App\Models\Admin;
use App\Notifications\PasswordChanged;
use App\Repositories\AdminRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class AuthAdminService
{
    public function __construct(
        protected AdminRepository $adminRepository,
        protected SessionService $sessionService,
    ) {}

    public function login(Request $request, array $params): string
    {
        $guard = Auth::guard('admin');
        $credentials = [
            'email' => $params['email'],
            'password' => $params['password'],
        ];

        if ($guard->attemptWhen($credentials, fn (Admin $admin) => $admin->isActive(), (bool) ($params['remember'] ?? false))) {
            $this->sessionService->regenerate($request, 'admin');

            return SecurityConst::LOGIN_OK;
        }

        $admin = $guard->getLastAttempted();

        if ($admin instanceof Admin && ! $admin->isActive() && $guard->getProvider()->validateCredentials($admin, $credentials)) {
            return SecurityConst::LOGIN_INACTIVE;
        }

        return SecurityConst::LOGIN_FAILED;
    }

    public function logout(Request $request): void
    {
        $this->sessionService->logout($request, 'admin');
    }

    public function sendResetLinkEmail(array $params): void
    {
        try {
            $status = Password::broker('admins')->sendResetLink(['email' => $params['email']]);

            if ($status !== Password::RESET_LINK_SENT) {
                Log::info(__METHOD__, ['status' => $status]);
            }
        } catch (\Throwable $th) {
            $this->logError(__METHOD__, $th);
        }
    }

    public function resetPassword(array $params): bool
    {
        try {
            $status = Password::broker('admins')->reset(
                [
                    'email' => $params['email'],
                    'password' => $params['password'],
                    'password_confirmation' => $params['password_confirmation'] ?? $params['password'],
                    'token' => $params['token'],
                ],
                function (Admin $admin, string $password) {
                    $admin->forceFill(['password' => Hash::make($password)])->save();

                    $this->sessionService->terminateOtherSessions($admin);

                    event(new PasswordReset($admin));

                    $this->notifyPasswordChanged($admin);
                }
            );

            return $status === Password::PASSWORD_RESET;
        } catch (\Throwable $th) {
            $this->logError(__METHOD__, $th);

            return false;
        }
    }

    public function notifyPasswordChanged(Admin $admin): void
    {
        try {
            $admin->notify((new PasswordChanged())->locale(config('app.locale')));
        } catch (\Throwable $th) {
            $this->logError(__METHOD__, $th);
        }
    }

    protected function logError(string $method, \Throwable $th): void
    {
        Log::error($method, [
            'message' => $th->getMessage(),
            'file' => $th->getFile(),
            'line' => $th->getLine(),
        ]);
    }
}
