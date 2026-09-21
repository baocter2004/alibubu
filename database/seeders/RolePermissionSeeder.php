<?php

namespace Database\Seeders;

use App\Services\Admin\RoleService;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(RoleService::class)->seedDefaults();
    }
}
