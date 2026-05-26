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
        Schema::create('crew_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique(); // PIC, FO, FA, SCCM
            $table->string('name');
            $table->enum('type', ['flight_deck', 'cabin']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crew_roles');
    }
};
