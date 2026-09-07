<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0)->after('thumbnail');
        });

        DB::table('products')
            ->where('type', 1)
            ->orderBy('id')
            ->get(['id', 'stock'])
            ->each(function ($product) {
                $variants = DB::table('product_variants')
                    ->where('product_id', $product->id)
                    ->orderBy('id')
                    ->pluck('id');

                if ($variants->isEmpty()) {
                    return;
                }

                $base = intdiv((int) $product->stock, $variants->count());
                $remainder = (int) $product->stock % $variants->count();

                foreach ($variants as $index => $variantId) {
                    DB::table('product_variants')
                        ->where('id', $variantId)
                        ->update(['stock' => $base + ($index < $remainder ? 1 : 0)]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
