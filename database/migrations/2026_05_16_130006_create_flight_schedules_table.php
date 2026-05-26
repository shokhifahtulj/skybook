<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('flight_id')->constrained('flights')->onDelete('cascade');
            
            $table->dateTime('departure_datetime')->index();
            $table->dateTime('arrival_datetime')->index();
            
            $table->string('status')->default('scheduled')->index();
            
            $table->string('terminal')->nullable();
            $table->string('gate')->nullable();
            
            $table->integer('available_seats')->default(0);
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_schedules');
    }
};
