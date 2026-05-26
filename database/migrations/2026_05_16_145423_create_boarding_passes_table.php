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
        Schema::create('boarding_passes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('booking_segment_passenger_id')->constrained('booking_segment_passengers')->cascadeOnDelete();
            
            $table->string('boarding_pass_number')->unique();
            $table->string('status')->default('generated'); // generated, active, scanned, boarded, revoked, expired
            
            // Operational Snapshots
            $table->string('gate_snapshot')->nullable();
            $table->string('boarding_group_snapshot')->nullable();
            $table->timestamp('boarding_time_snapshot')->nullable();
            
            $table->text('qr_signature')->nullable();
            $table->string('pdf_path')->nullable();
            
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
            
            // For scanning queries
            $table->index(['booking_segment_passenger_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boarding_passes');
    }
};
