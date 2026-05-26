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
        Schema::create('predictive_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('flight_schedule_id')->constrained('flight_schedules')->onDelete('cascade');
            $table->string('prediction_type'); // GATE_CONFLICT, ROTATION_DELAY
            $table->string('severity'); // LOW, MEDIUM, HIGH, CRITICAL
            $table->text('description');
            $table->integer('confidence_score')->default(0); // 0-100
            $table->integer('forecast_window_minutes')->default(0);
            $table->timestamp('predicted_at');
            $table->timestamp('resolved_at')->nullable();
            $table->string('resolution_method')->nullable();
            $table->string('status')->default('PREDICTED'); // PREDICTED, CONFIRMED, MITIGATED, EXPIRED, FALSE_POSITIVE
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictive_alerts');
    }
};
