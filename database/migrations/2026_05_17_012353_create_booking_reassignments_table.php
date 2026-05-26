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
        Schema::create('booking_reassignments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('booking_segment_passenger_id')->constrained('booking_segment_passengers')->onDelete('cascade');
            $table->foreignUuid('from_flight_schedule_id')->constrained('flight_schedules')->onDelete('cascade');
            $table->foreignUuid('to_flight_schedule_id')->constrained('flight_schedules')->onDelete('cascade');
            $table->string('reason'); // IROPS, voluntary
            $table->string('triggered_by_event')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_reassignments');
    }
};
