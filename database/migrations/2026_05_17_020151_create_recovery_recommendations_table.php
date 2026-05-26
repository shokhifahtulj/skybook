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
        Schema::create('recovery_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('flight_schedule_id')->constrained('flight_schedules')->onDelete('cascade');
            $table->foreignId('simulation_session_id')->nullable()->constrained('simulation_sessions')->nullOnDelete();
            $table->json('recommendation_payload');
            $table->string('selected_strategy_id')->nullable();
            $table->integer('final_score')->nullable();
            $table->string('execution_outcome')->nullable(); // SUCCESS, PARTIAL_SUCCESS, FAILED, OVERRIDDEN
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recovery_recommendations');
    }
};
