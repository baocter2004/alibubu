<?php

use App\Const\OrderConst;
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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')
                ->default(OrderConst::STATUS_PENDING)
                ->after('total_amount')
                ->index();

            $table->timestamp('confirmed_at')->nullable()->after('status');
            $table->timestamp('shipped_at')->nullable()->after('confirmed_at');
            $table->timestamp('completed_at')->nullable()->after('shipped_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
            $table->timestamp('returned_at')->nullable()->after('cancelled_at');
            $table->string('cancel_reason')->nullable()->after('returned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'confirmed_at', 'shipped_at', 'completed_at', 'cancelled_at', 'returned_at', 'cancel_reason']);
        });
    }
};
