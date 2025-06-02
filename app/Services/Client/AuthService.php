<?php

namespace App\Services\Client;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            throw $th;
        }
    }

    public function login(array $params)
    {
        try {
            if (Auth::attempt(['email' => $params['email'], 'password' => $params['password']])) {
                request()->session()->regenerate();
                return true;
            }
            return false;
        } catch (\Throwable $th) {
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
                    'fullname'      => $googleUser->getName(),
                    'email'         => $email,
                    'google_id'     => $googleUser->getId(),
                    'password'      => Hash::make(Str::random(24)),
                ]);
            } else {
                if ($user->status == 3) {
                    return null;
                }
            }

            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
