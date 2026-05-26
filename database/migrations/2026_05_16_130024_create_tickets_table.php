<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('booking_segment_passenger_id')->constrained('booking_segment_passengers')->cascadeOnDelete();
                
            $table->string('ticket_number')->unique();
            $table->string('ticket_status')->default('issued');
            
            $table->string('document_path')->nullable();
            $table->json('snapshot_data')->nullable();
            
            $table->timestamp('issued_at')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
