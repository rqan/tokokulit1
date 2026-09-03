<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('technical_specs')->nullable();
            $table->string('availability_status')->default('Available'); // Available, Waitlist, Discontinued
            $table->string('provenance')->nullable(); // e.g. "Swiss Made", "COSC Certified"
            $table->text('care_instructions')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['technical_specs', 'availability_status', 'provenance', 'care_instructions']);
        });
    }
};
