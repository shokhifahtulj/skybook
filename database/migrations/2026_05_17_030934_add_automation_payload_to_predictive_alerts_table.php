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
        Schema::table('predictive_alerts', function (Blueprint $table) {
            $table->json('automation_payload')->nullable()->after('resolution_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('predictive_alerts', function (Blueprint $table) {
            //
        });
    }
};
