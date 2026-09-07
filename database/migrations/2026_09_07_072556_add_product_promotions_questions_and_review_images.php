<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_promotions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->string('content');
            $table->string('icon')->nullable();
            $table->unsignedInteger('ordinal')->default(0);
            $table->timestamps();
        });

        Schema::create('product_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('fullname')->nullable();
            $table->text('question');
            $table->text('answer')->nullable();
            $table->uuid('answered_by')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['product_id', 'is_published']);
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->json('images')->nullable()->after('comment');
        });
    }

    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropColumn('images');
        });

        Schema::dropIfExists('product_questions');
        Schema::dropIfExists('product_promotions');
    }
};
