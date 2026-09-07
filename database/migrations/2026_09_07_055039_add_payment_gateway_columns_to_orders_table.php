<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('payment_reference');
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway')->default('vnpay');
            $table->string('reference')->index();
            $table->string('transaction_no')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('response_code')->nullable();
            $table->decimal('amount', 13, 2)->default(0);
            $table->boolean('is_successful')->default(false);
            $table->string('source')->default('return');
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'paid_at']);
        });
    }
};
