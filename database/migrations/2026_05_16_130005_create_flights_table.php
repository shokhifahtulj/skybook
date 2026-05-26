<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('flight_number')->unique(); // e.g. GA210
            $table->foreignUuid('airline_id')->constrained('airlines')->onDelete('cascade');
            $table->foreignUuid('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignUuid('aircraft_id')->nullable()->constrained('aircrafts')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
