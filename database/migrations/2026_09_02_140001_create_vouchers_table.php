<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vouchers')) {
            Schema::create('vouchers', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique();
                $table->enum('type', ['fixed', 'percent'])->default('fixed');
                $table->decimal('amount', 15, 2);
                $table->decimal('min_spend', 15, 2)->default(0);
                $table->decimal('max_discount', 15, 2)->nullable();
                $table->integer('usage_limit')->nullable();
                $table->integer('used_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
