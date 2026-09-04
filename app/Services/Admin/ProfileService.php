<?php

namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function updateProfile(Admin $admin, array $params): Admin
    {
        $admin->update([
            'name' => $params['name'],
            'email' => $params['email'],
        ]);

        return $admin->refresh();
    }

    public function updatePassword(Admin $admin, string $password): void
    {
        $admin->update(['password' => Hash::make($password)]);
    }
}
