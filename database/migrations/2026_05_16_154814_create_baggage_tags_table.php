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
        Schema::create('baggage_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('booking_passenger_ancillary_id')->constrained()->cascadeOnDelete();
            $table->string('tag_number')->unique();
            $table->decimal('weight_kg', 5, 2);
            $table->string('destination_airport_code');
            $table->enum('status', ['generated', 'checked_in', 'loaded', 'unloaded', 'delivered', 'lost'])->default('generated');
            $table->text('signature')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('loaded_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baggage_tags');
    }
};
