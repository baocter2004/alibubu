<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('membership_tier')->nullable()->after('coupon_discount_value');
            $table->decimal('membership_discount', 13, 2)->default(0)->after('membership_tier');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['membership_tier', 'membership_discount']);
        });
    }
};
