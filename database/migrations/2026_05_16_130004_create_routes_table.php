<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('origin_airport_id')->constrained('airports')->onDelete('restrict');
            $table->foreignUuid('destination_airport_id')->constrained('airports')->onDelete('restrict');
            $table->integer('distance')->nullable(); // in km
            $table->integer('estimated_duration')->nullable(); // in minutes
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
