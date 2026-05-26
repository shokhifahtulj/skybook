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
        Schema::table('aircrafts', function (Blueprint $table) {
            $table->enum('operational_status', [
                'available', 
                'assigned', 
                'maintenance', 
                'grounded', 
                'delayed_rotation', 
                'out_of_service'
            ])->default('available')->after('seat_layout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aircrafts', function (Blueprint $table) {
            $table->dropColumn('operational_status');
        });
    }
};
