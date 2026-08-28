<?php

namespace App\Services\Auth;

use App\Const\UserConst;
use App\Mail\VerifyUserEmail;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(protected UserRepository $userRepository) {}

    public function register(array $params): ?User
    {
        try {
            $user = $this->userRepository->create([
                'fullname' => $params['fullname'],
                'email' => $params['email'],
                'password' => Hash::make($params['password']),
                'role' => UserConst::ROLE_USER,
                'status' => UserConst::STATUS_ACTIVE,
            ]);

            Mail::to($user->email)->queue(new VerifyUserEmail($user));

            return $user;
        } catch (\Throwable $th) {
            Log::error(__METHOD__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            return null;
        }
    }

    public function login(array $params): bool
    {
        $credentials = [
            'email' => $params['email'],
            'password' => $params['password'],
        ];

        if (! Auth::attempt($credentials, (bool) ($params['remember'] ?? false))) {
            return false;
        }

        request()->session()->regenerate();

        return true;
    }

    public function google($googleUser): ?User
    {
        try {
            $email = $googleUser->getEmail();
            $user = $this->userRepository->findBy($email, 'email');

            if ($user) {
                if ((int) $user->status === UserConst::STATUS_LOCKED) {
                    return null;
                }

                if (empty($user->google_id)) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }

                return $user;
            }

            $user = $this->userRepository->create([
                'fullname' => $googleUser->getName() ?: $email,
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'password' => Hash::make(Str::random(32)),
                'role' => UserConst::ROLE_USER,
                'status' => UserConst::STATUS_ACTIVE,
            ]);

            Mail::to($user->email)->queue(new VerifyUserEmail($user));

            return $user;
        } catch (\Throwable $th) {
            Log::error(__METHOD__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            return null;
        }
    }

    public function sendResetLinkEmail(array $params): bool
    {
        try {
            $status = Password::broker()->sendResetLink(['email' => $params['email']]);

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

    public function reset(array $params): bool
    {
        try {
            $status = Password::broker()->reset(
                [
                    'email' => $params['email'],
                    'password' => $params['password'],
                    'password_confirmation' => $params['password_confirmation'] ?? $params['password'],
                    'token' => $params['token'],
                ],
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));
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
