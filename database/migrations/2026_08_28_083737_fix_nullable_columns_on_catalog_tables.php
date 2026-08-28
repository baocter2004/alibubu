<?php

use App\Const\UserConst;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('logo')->nullable()->change();
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->change();
            $table->boolean('is_active')->default(true)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_sale')->default(false)->change();
            $table->boolean('is_featured')->default(false)->change();
            $table->boolean('is_trending')->default(false)->change();
            $table->boolean('is_active')->default(true)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('status')->default(UserConst::STATUS_ACTIVE)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('logo')->nullable(false)->change();
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('thumbnail')->nullable(false)->change();
            $table->boolean('is_active')->default(null)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_sale')->default(null)->change();
            $table->boolean('is_featured')->default(null)->change();
            $table->boolean('is_trending')->default(null)->change();
            $table->boolean('is_active')->default(null)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('status')->nullable()->change();
        });
    }
};
