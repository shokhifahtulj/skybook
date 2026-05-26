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
        Schema::table('flight_schedules', function (Blueprint $table) {
            $table->foreignId('departure_gate_id')->nullable()->constrained('airport_gates')->nullOnDelete();
            $table->foreignId('arrival_gate_id')->nullable()->constrained('airport_gates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flight_schedules', function (Blueprint $table) {
            $table->dropForeign(['departure_gate_id']);
            $table->dropForeign(['arrival_gate_id']);
            $table->dropColumn(['departure_gate_id', 'arrival_gate_id']);
        });
    }
};
