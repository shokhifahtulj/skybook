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
        Schema::create('simulation_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('scenario_type');
            $table->string('scenario_seed');
            $table->json('baseline_snapshot');
            $table->json('kpi_snapshot')->nullable();
            $table->string('status')->default('ACTIVE'); // ACTIVE, RESTORED
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_sessions');
    }
};
