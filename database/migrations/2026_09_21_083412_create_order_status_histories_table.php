<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('from_status')->nullable();
            $table->unsignedTinyInteger('to_status')->nullable();
            $table->string('event', 40);
            $table->string('actor_type', 20);
            $table->string('actor_id', 36)->nullable();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
