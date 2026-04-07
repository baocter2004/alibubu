<?php

use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
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
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->foreignIdFor(Province::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->after('user_id');

            $table->foreignIdFor(Ward::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->after('province_id');

            $table->string('province')->nullable()->after('ward_id');
            $table->string('ward')->nullable()->after('province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['ward_id']);

            $table->dropColumn([
                'province_id',
                'ward_id',
                'province',
                'ward'
            ]);
        });
    }
};
