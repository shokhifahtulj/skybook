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
        Schema::create('notification_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('booking_segment_passenger_id')->constrained('booking_segment_passengers')->onDelete('cascade');
            $table->foreignUuid('flight_schedule_id')->nullable()->constrained('flight_schedules')->nullOnDelete();
            $table->string('event_type');
            $table->string('channel'); // EMAIL, SMS, IN_APP
            $table->string('recipient');
            $table->string('idempotency_key')->unique();
            $table->integer('message_version')->default(1);
            $table->json('payload_snapshot');
            $table->string('priority_level')->default('MEDIUM'); // LOW, MEDIUM, HIGH, CRITICAL
            $table->string('delivery_status')->default('PENDING'); // PENDING, SENT, FAILED
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_deliveries');
    }
};
