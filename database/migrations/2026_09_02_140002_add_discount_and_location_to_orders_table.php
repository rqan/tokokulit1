<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'voucher_code')) {
                $table->string('voucher_code', 50)->nullable();
            }
            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'province_id')) {
                $table->string('province_id', 20)->nullable();
            }
            if (!Schema::hasColumn('orders', 'city_id')) {
                $table->string('city_id', 20)->nullable();
            }
            if (!Schema::hasColumn('orders', 'weight_grams')) {
                $table->integer('weight_grams')->default(1000);
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['voucher_code', 'discount_amount', 'province_id', 'city_id', 'weight_grams']);
        });
    }
};
