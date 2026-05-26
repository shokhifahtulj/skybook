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
            $table->uuid('aircraft_id')->nullable()->after('flight_id');
            $table->foreign('aircraft_id')->references('id')->on('aircrafts')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flight_schedules', function (Blueprint $table) {
            $table->dropForeign(['aircraft_id']);
            $table->dropColumn('aircraft_id');
        });
    }
};
