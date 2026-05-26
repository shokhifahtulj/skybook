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
        Schema::create('ancillary_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique(); // e.g., BG15, PRIO
            $table->string('name'); // e.g., Extra Baggage 15kg
            $table->string('type')->index(); // e.g., baggage, priority_boarding, meal
            $table->text('description')->nullable();
            $table->decimal('base_price', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ancillary_services');
    }
};
