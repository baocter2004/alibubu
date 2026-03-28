<?php

namespace App\Services\Auth;

use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthAdminService
{
    function __construct(protected AdminRepository $adminRepository) {}
    public function login(array $params)
    {
        try {
            if (Auth::guard('admin')->attempt([
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
}
