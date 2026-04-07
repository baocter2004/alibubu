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
            $table->foreignIdFor(Province::class)->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignIdFor(Ward::class)->nullable()->after('province_id')->constrained()->nullOnDelete();

            $table->string('province')->after('ward_id')->nullable();
            $table->string('ward')->after('province')->nullable();
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
