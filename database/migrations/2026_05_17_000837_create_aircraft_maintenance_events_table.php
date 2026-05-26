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
        Schema::create('aircraft_maintenance_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('aircraft_id');
            $table->enum('maintenance_type', ['scheduled', 'unscheduled', 'aog', 'inspection', 'line_maintenance', 'heavy_maintenance']);
            $table->enum('status', ['planned', 'in_progress', 'completed', 'deferred'])->default('planned');
            $table->enum('severity', ['minor', 'major', 'critical'])->default('major');
            $table->datetime('start_at');
            $table->datetime('end_at');
            $table->text('notes')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('dispatch_released_at')->nullable();
            $table->foreignId('dispatch_released_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('aircraft_id')->references('id')->on('aircrafts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircraft_maintenance_events');
    }
};
