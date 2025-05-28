<?php

use App\Const\CouponConst;
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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('title', 50);
            $table->string('description')->nullable();
            $table->tinyInteger('discount_type')->default(CouponConst::FIX_AMOUNT);
            $table->decimal('discount_value', 10, 2);
            $table->unsignedSmallInteger('usage_limit');
            $table->unsignedInteger('usage_count');
            $table->boolean('is_expired')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
