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
        Schema::table('flight_crew_assignments', function (Blueprint $table) {
            $table->uuid('previous_assignment_id')->nullable()->after('crew_role_id');
            $table->uuid('next_assignment_id')->nullable()->after('previous_assignment_id');
            
            $table->foreign('previous_assignment_id')->references('id')->on('flight_crew_assignments')->nullOnDelete();
            $table->foreign('next_assignment_id')->references('id')->on('flight_crew_assignments')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flight_crew_assignments', function (Blueprint $table) {
            $table->dropForeign(['previous_assignment_id']);
            $table->dropForeign(['next_assignment_id']);
            $table->dropColumn(['previous_assignment_id', 'next_assignment_id']);
        });
    }
};
