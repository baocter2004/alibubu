<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_admin_is_seeded_in_testing_environment(): void
    {
        (new AdminSeeder())->run();

        $this->assertDatabaseHas('admins', ['email' => 'admin@alibubu.test']);
    }

    public function test_seeder_skips_demo_admin_outside_local_and_testing(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        (new AdminSeeder())->run();

        $this->assertDatabaseMissing('admins', ['email' => 'admin@alibubu.test']);

        $this->app->detectEnvironment(fn () => 'testing');
    }

    public function test_initial_admin_is_created_from_env_without_resetting_existing_password(): void
    {
        config(['auth.initial_admin.name' => 'Env Admin', 'auth.initial_admin.email' => 'env-admin@example.com', 'auth.initial_admin.password' => 'InitialPass123!']);

        (new AdminSeeder())->run();

        $admin = Admin::where('email', 'env-admin@example.com')->firstOrFail();
        $originalHash = $admin->password;

        config(['auth.initial_admin.password' => 'SomeOtherPassword456!']);
        (new AdminSeeder())->run();

        $this->assertSame($originalHash, $admin->fresh()->password);
    }
}
