<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_questions', function (Blueprint $table) {
            $table->string('email')->nullable()->after('fullname');
            $table->string('ip_address', 45)->nullable()->after('email');
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            $table->string('rejection_reason', 500)->nullable()->after('rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropColumn(['rejected_at', 'rejection_reason']);
        });

        Schema::table('product_questions', function (Blueprint $table) {
            $table->dropColumn(['email', 'ip_address']);
        });
    }
};
