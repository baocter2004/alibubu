<?php

namespace Database\Seeders;

use App\Const\AdminConst;
use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
