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
        Schema::create('crew_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('crew_role_id');
            $table->string('crew_code')->unique(); // e.g., PIC-00012
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->uuid('base_airport_id'); // airport where the crew is based
            $table->enum('operational_status', ['available', 'assigned', 'off_duty', 'medical_leave', 'inactive', 'training'])->default('available');
            $table->string('license_number')->nullable();
            $table->date('license_expiry')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('crew_role_id')->references('id')->on('crew_roles')->onDelete('restrict');
            $table->foreign('base_airport_id')->references('id')->on('airports')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crew_members');
    }
};
