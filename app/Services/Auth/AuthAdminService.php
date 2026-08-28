<?php

namespace App\Services\Auth;

use App\Repositories\AdminRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthAdminService
{
    public function __construct(protected AdminRepository $adminRepository) {}

    public function login(array $params): bool
    {
        $credentials = [
            'email' => $params['email'],
            'password' => $params['password'],
        ];

        if (! Auth::guard('admin')->attempt($credentials, (bool) ($params['remember'] ?? false))) {
            return false;
        }

        request()->session()->regenerate();

        return true;
    }

    public function sendResetLinkEmail(array $params): bool
    {
        try {
            $status = Password::broker('admins')->sendResetLink(['email' => $params['email']]);

            return $status === Password::RESET_LINK_SENT;
        } catch (\Throwable $th) {
            Log::error(__METHOD__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            return false;
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
                function ($admin, $password) {
                    $admin->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($admin));
                }
            );

            return $status === Password::PASSWORD_RESET;
        } catch (\Throwable $th) {
            Log::error(__METHOD__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            return false;
        }
    }
}
