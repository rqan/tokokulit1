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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 30)->unique()->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->string('shipping_name', 255);
            $table->string('shipping_email', 255);
            $table->string('shipping_phone', 30);
            $table->text('shipping_address');
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('pending_confirmation');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('shipping_fee', 15, 2)->nullable();
            $table->decimal('additional_fee', 15, 2)->default(0);
            $table->string('additional_fee_note', 255)->nullable();
            $table->decimal('grand_total', 15, 2)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->string('shipping_courier', 100)->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->string('rating_token', 64)->unique()->nullable();
            $table->timestamp('rating_sent_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('user_id');
            $table->index('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
