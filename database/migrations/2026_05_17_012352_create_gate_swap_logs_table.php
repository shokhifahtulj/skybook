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
        Schema::create('gate_swap_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('flight_schedule_id')->constrained('flight_schedules')->onDelete('cascade');
            $table->foreignId('old_gate_id')->nullable()->constrained('airport_gates')->nullOnDelete();
            $table->foreignId('new_gate_id')->constrained('airport_gates')->onDelete('cascade');
            $table->foreignId('swapped_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->string('swap_type')->default('departure'); // departure, arrival
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_swap_logs');
    }
};
