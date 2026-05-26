<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('airports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('iata_code', 10)->unique(); // e.g. CGK
            $table->string('name'); // e.g. Soekarno-Hatta International Airport
            $table->string('city'); // e.g. Jakarta
            $table->string('country')->default('Indonesia');
            $table->string('timezone')->default('Asia/Jakarta');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('airports');
    }
};
