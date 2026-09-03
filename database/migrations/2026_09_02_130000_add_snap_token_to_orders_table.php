<?php

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
            if (!Schema::hasColumn('orders', 'snap_token')) {
                $table->string('snap_token', 255)->nullable();
            }
            if (!Schema::hasColumn('orders', 'payment_gateway_ref')) {
                $table->string('payment_gateway_ref', 255)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'payment_gateway_ref']);
        });
    }
};
