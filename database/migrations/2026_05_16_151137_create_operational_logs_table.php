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
        Schema::create('operational_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_type')->index();
            $table->string('entity_type')->nullable();
            $table->uuid('entity_id')->nullable();
            $table->foreignUuid('flight_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignUuid('passenger_id')->nullable()->constrained('passengers')->nullOnDelete();
            
            // Siapa yang memicu (System, Gate Agent, Passenger)
            $table->string('actor_type')->nullable();
            $table->string('actor_id')->nullable();
            
            // Status Level: info, warning, danger (untuk fraud/anomali)
            $table->string('level')->default('info')->index();
            
            // JSON Payload Snapshot
            $table->json('event_payload')->nullable();
            
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            // Hanya created_at (Immutable)
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_logs');
    }
};
