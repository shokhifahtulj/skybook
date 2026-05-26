<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aircrafts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('airline_id')->constrained('airlines')->onDelete('cascade');
            $table->string('model'); // e.g. Boeing 737-800
            $table->integer('capacity'); // e.g. 189
            $table->string('seat_layout')->default('3-3'); // e.g. 3-3, 2-4-2
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aircrafts');
    }
};
