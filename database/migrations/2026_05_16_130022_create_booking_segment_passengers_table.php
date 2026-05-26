<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_segment_passengers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('booking_segment_id')->constrained('booking_segments')->cascadeOnDelete();
            $table->foreignUuid('passenger_id')->constrained('passengers')->cascadeOnDelete();
            $table->foreignId('flight_schedule_seat_id')->nullable()->constrained('flight_schedule_seats')->nullOnDelete();
            
            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['booking_segment_id', 'passenger_id'], 'bsp_seg_pass_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_segment_passengers');
    }
};
