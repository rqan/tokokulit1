<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create offline_stores table if missing
        if (!Schema::hasTable('offline_stores')) {
            Schema::create('offline_stores', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->text('address')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Add is_active to online_stores if missing
        if (Schema::hasTable('online_stores') && !Schema::hasColumn('online_stores', 'is_active')) {
            Schema::table('online_stores', function (Blueprint $table) {
                $table->boolean('is_active')->default(true);
            });
        }

        // Add performance indexes
        if (Schema::hasTable('products') && !$this->hasIndex('products', 'products_category_index')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index('category');
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!$this->hasIndex('orders', 'orders_payment_gateway_ref_index') && Schema::hasColumn('orders', 'payment_gateway_ref')) {
                    $table->index('payment_gateway_ref');
                }
            });
        }

        // Add gender column to products
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'gender')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('gender')->nullable(); // pria, wanita, unisex
            });
        }

        if (Schema::hasTable('vouchers') && !$this->hasIndex('vouchers', 'vouchers_code_index')) {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->index(['code', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offline_stores');
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        try {
            $indexes = Schema::getIndexes($table);
            foreach ($indexes as $index) {
                if ($index['name'] === $indexName) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            // Fallback: assume index doesn't exist
        }
        return false;
    }
};
