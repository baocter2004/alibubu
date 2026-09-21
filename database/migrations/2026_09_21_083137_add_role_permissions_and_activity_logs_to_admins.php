<?php

use App\Const\AdminConst;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->unsignedTinyInteger('role')->default(AdminConst::ROLE_STAFF)->change();
            $table->boolean('is_active')->default(true)->after('role');
        });

        Schema::create('admin_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('role');
            $table->string('permission', 100);
            $table->timestamps();

            $table->unique(['role', 'permission']);
        });

        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('action', 100)->index();
            $table->string('subject_type')->nullable();
            $table->string('subject_id', 64)->nullable();
            $table->json('properties')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
        Schema::dropIfExists('admin_role_permissions');

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->unsignedTinyInteger('role')->default(AdminConst::ROLE_SUPER_ADMIN)->change();
        });
    }
};
