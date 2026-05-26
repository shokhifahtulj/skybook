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
        Schema::create('gate_occupancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gate_id')->constrained('airport_gates')->onDelete('cascade');
            $table->foreignUuid('flight_schedule_id')->constrained('flight_schedules')->onDelete('cascade');
            $table->dateTime('occupied_from');
            $table->dateTime('occupied_until');
            $table->string('occupancy_type'); // departure, arrival
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_occupancies');
    }
};
