<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
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
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained();
            $table->foreignUuid('product_id')->nullable()->constrained();
            $table->foreignUuid('product_variant_id')->nullable()->constrained();
            $table->string('name')->nullable();
            $table->decimal('price', 11, 2)->nullable();
            $table->decimal('old_price', 11, 2)->nullable();
            $table->decimal('old_price_variant', 11, 2)->nullable();

            $table->unsignedSmallInteger('quantity')->nullable();
            $table->string('name_variant')->nullable();
            $table->jsonb('attributes_variant')->nullable();
            $table->decimal('price_variant', 11, 2)->nullable();
            $table->unsignedSmallInteger('quantity_variant')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
