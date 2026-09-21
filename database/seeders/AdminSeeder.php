<?php

namespace Database\Seeders;

use App\Const\AdminConst;
use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('auth.initial_admin.email');
        $password = config('auth.initial_admin.password');

        if (filled($email) && filled($password) && ! Admin::where('email', $email)->exists()) {
            Admin::create([
                'name' => config('auth.initial_admin.name'),
                'email' => $email,
                'password' => $password,
                'role' => AdminConst::ROLE_SUPER_ADMIN,
            ]);
        }

        if (app()->environment(['local', 'testing'])) {
            Admin::updateOrCreate(
                ['email' => 'admin@alibubu.test'],
                [
                    'name' => 'Quản trị viên',
                    'password' => 'password',
                    'role' => AdminConst::ROLE_SUPER_ADMIN,
                ]
            );
        }
    }
}
