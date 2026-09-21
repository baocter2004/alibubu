<?php

use App\Const\PaymentConst;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('payment_status')
                ->default(PaymentConst::STATUS_UNPAID)
                ->after('payment_method')
                ->index();
            $table->string('payment_reference')->nullable()->unique()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('payment_reference');
            $table->timestamp('payment_expires_at')->nullable()->index()->after('paid_at');
            $table->timestamp('refunded_at')->nullable()->after('payment_expires_at');
            $table->string('refund_note', 500)->nullable()->after('refunded_at');
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway')->default(PaymentConst::GATEWAY_VNPAY);
            $table->string('type', 20)->default(PaymentConst::TYPE_PAYMENT);
            $table->string('reference')->index();
            $table->string('transaction_no')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('response_code')->nullable();
            $table->decimal('amount', 13, 2)->default(0);
            $table->boolean('is_successful')->default(false);
            $table->string('source')->default(PaymentConst::SOURCE_RETURN);
            $table->foreignUuid('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('note', 500)->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['gateway', 'transaction_no']);
            $table->index(['gateway', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['payment_reference']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['payment_expires_at']);
            $table->dropColumn([
                'payment_status',
                'payment_reference',
                'paid_at',
                'payment_expires_at',
                'refunded_at',
                'refund_note',
            ]);
        });
    }
};
