<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_segments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignUuid('flight_schedule_id')->constrained('flight_schedules')->cascadeOnDelete();
            
            $table->integer('segment_order')->default(1);
            $table->string('cabin_class');
            $table->string('segment_status')->default('scheduled');
            
            $table->decimal('fare_snapshot', 15, 2)->default(0);
            $table->decimal('tax_snapshot', 15, 2)->default(0);
            
            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['booking_id', 'flight_schedule_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_segments');
    }
};
