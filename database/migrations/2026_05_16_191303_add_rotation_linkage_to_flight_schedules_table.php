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
            $table->uuid('previous_schedule_id')->nullable()->after('aircraft_id');
            $table->uuid('next_schedule_id')->nullable()->after('previous_schedule_id');
            
            $table->integer('delay_minutes')->default(0)->after('status');
            $table->string('delay_source')->nullable()->after('delay_minutes'); // e.g. manual, rotation, weather, maintenance
            $table->string('delay_reason')->nullable()->after('delay_source');
            
            $table->foreign('previous_schedule_id')->references('id')->on('flight_schedules')->nullOnDelete();
            $table->foreign('next_schedule_id')->references('id')->on('flight_schedules')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flight_schedules', function (Blueprint $table) {
            $table->dropForeign(['previous_schedule_id']);
            $table->dropForeign(['next_schedule_id']);
            $table->dropColumn(['previous_schedule_id', 'next_schedule_id', 'delay_minutes', 'delay_source', 'delay_reason']);
        });
    }
};
