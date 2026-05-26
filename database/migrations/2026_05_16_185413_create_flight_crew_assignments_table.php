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
        Schema::create('flight_crew_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('flight_schedule_id');
            $table->uuid('crew_member_id');
            $table->uuid('crew_role_id');
            $table->dateTime('assigned_at');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('restrict'); // user id who assigned
            $table->enum('status', ['assigned', 'replaced', 'removed'])->default('assigned');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('flight_schedule_id')->references('id')->on('flight_schedules')->onDelete('cascade');
            $table->foreign('crew_member_id')->references('id')->on('crew_members')->onDelete('restrict');
            $table->foreign('crew_role_id')->references('id')->on('crew_roles')->onDelete('restrict');

            // Ensure a crew member is not assigned twice to the same flight
            $table->unique(['flight_schedule_id', 'crew_member_id'], 'unique_crew_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_crew_assignments');
    }
};
