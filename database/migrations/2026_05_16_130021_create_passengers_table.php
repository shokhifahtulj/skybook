<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();
            
            $table->string('title');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('identity_type');
            $table->string('identity_number');
            $table->date('date_of_birth');
            $table->string('nationality')->default('ID');
            $table->string('passenger_type')->default('adult');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};
