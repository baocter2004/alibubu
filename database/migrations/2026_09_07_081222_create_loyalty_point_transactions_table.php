<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_point_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('order_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('points');
            $table->string('type')->default('order');
            $table->string('description')->nullable();
            $table->timestamp('earned_at')->index();
            $table->timestamps();

            $table->index(['user_id', 'earned_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('membership_tier')->default('member')->after('loyalty_points');
            $table->timestamp('tier_reviewed_at')->nullable()->after('membership_tier');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['membership_tier', 'tier_reviewed_at']);
        });

        Schema::dropIfExists('loyalty_point_transactions');
    }
};
