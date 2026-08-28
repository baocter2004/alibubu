<?php

use App\Models\Coupon;
use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
          $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->foreignUuid('user_id')->nullable()->constrained();
            $table->string('phone_number', 20);
            $table->string('email')->nullable();
            $table->string('fullname');
            $table->text('address');
            $table->text('note')->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_refund')->default(0);
            $table->boolean('locked_status')->default(0);
            $table->foreignUuid('coupon_id')->nullable()->constrained();
            $table->string('coupon_code', 50)->nullable();
            $table->string('coupon_description')->nullable();
            $table->string('coupon_discount_type', 20)->nullable();
            $table->decimal('coupon_discount_value', 10, 2)->nullable();
            $table->decimal('max_discount_value', 11, 2)->nullable();
            $table->boolean('is_refund_cancel')->default(null)->nullable();
            $table->boolean('check_refund_cancel')->default(null)->nullable();
            $table->string('img_send_refund_money', 255)->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
