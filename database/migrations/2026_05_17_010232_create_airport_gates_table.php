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
        Schema::create('airport_gates', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('airport_id')->constrained('airports')->onDelete('cascade');
            $table->string('terminal');
            $table->string('gate_number');
            $table->string('status')->default('active'); // active, closed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airport_gates');
    }
};
