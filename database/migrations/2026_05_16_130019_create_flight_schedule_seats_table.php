<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_schedule_seats', function (Blueprint $table) {
            $table->id();
            
            $table->foreignUuid('flight_schedule_id')->constrained('flight_schedules')->cascadeOnDelete();
            $table->foreignId('aircraft_seat_id')->constrained('aircraft_seats')->cascadeOnDelete();
            
            // Snapshot fields
            $table->string('seat_number')->index();
            $table->string('cabin_class')->index();
            $table->boolean('is_window')->default(false);
            $table->boolean('is_aisle')->default(false);
            $table->boolean('is_exit_row')->default(false);
            
            $table->string('status')->default('available')->index();
            
            $table->foreignUuid('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            
            // Lock ownership
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('lock_session')->nullable()->index();
            
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('booked_at')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['flight_schedule_id', 'seat_number']);
            $table->index(['flight_schedule_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_schedule_seats');
    }
};
