<?php

namespace App\Services\Auth;

use App\Repositories\UserRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Str;

class AuthService
{
    protected $userRepository;

    function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $params)
    {
        try {
            $params['password'] = Hash::make($params['password']);
            $user = $this->userRepository->create($params);
            return $user;
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . __FUNCTION__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString()
            ]);
            throw $th;
        }
    }

    public function login(array $params)
    {
        try {
            if (Auth::attempt([
                'email' => $params['email'],
                'password' => $params['password'],

            ], $params['remember'] ?? false)) {
                request()->session()->regenerate();
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . __FUNCTION__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString()
            ]);
            throw $th;
        }
    }

    public function google($googleUser)
    {
        try {
            $email = $googleUser->getEmail();

            $user = $this->userRepository->findBy('email', $email);

            if (!$user) {
                $user = $this->userRepository->create([
                    'fullname' => $googleUser->getName(),
                    'email' => $email,
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(24)),
                ]);
            } else {
                if ($user->status == 3) {
                    return null;
                }
            }

            return $user;
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . __FUNCTION__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString()
            ]);

            throw $th;
        }
    }

    public function sendResetLinkEmail(array $params)
    {
        try {
            $status = Password::sendResetLink(['email' => $params['email']]);

            return $status === Password::RESET_LINK_SENT;
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . __FUNCTION__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString()
            ]);
            throw $th;
        }
    }

    public function reset(array $params)
    {
        try {
            $status = Password::reset(
                [
                    'email' => $params['email'],
                    'password' => $params['password'],
                    'token' => $params['token'],
                ],
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->setRememberToken(Str::random(60));
                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            return $status === Password::PASSWORD_RESET;
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . __FUNCTION__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString()
            ]);
            throw $th;
        }
    }
}
