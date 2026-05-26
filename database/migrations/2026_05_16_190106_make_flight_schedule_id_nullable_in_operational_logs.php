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
        Schema::table('operational_logs', function (Blueprint $table) {
            // Need to drop foreign key first before changing in some DBs, but let's try direct change
            $table->uuid('flight_schedule_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operational_logs', function (Blueprint $table) {
            $table->uuid('flight_schedule_id')->nullable(false)->change();
        });
    }
};
