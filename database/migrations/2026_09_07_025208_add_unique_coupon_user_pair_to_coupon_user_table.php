<?php

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
        Schema::table('coupon_user', function (Blueprint $table) {
            $table->unique(
                ['coupon_id', 'user_id'],
                'coupon_user_coupon_id_user_id_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupon_user', function (Blueprint $table) {
            $table->dropUnique('coupon_user_coupon_id_user_id_unique');
        });
    }
};
