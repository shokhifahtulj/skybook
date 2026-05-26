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
        Schema::create('booking_passenger_ancillaries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('booking_segment_passenger_id');
            $table->foreign('booking_segment_passenger_id', 'fk_bpa_seg_pass')
                  ->references('id')->on('booking_segment_passengers')
                  ->cascadeOnDelete();
                  
            $table->foreignUuid('ancillary_service_id');
            $table->foreign('ancillary_service_id', 'fk_bpa_ancillary')
                  ->references('id')->on('ancillary_services')
                  ->cascadeOnDelete();
            
            // Snapshots
            $table->string('type')->index();
            $table->string('snapshot_name');
            $table->decimal('snapshot_price', 12, 2);
            
            // Data spesifik layanan (misal weight_kg, bag_tag_uuid)
            $table->json('metadata')->nullable();
            
            // Payment / Commerce Lifecycle
            $table->string('status')->default('pending')->index(); // pending, paid, cancelled
            
            // Operational Lifecycle
            $table->string('operational_status')->default('not_used')->index(); // not_used, checked_in, consumed
            
            // Foreign key to a payment reference if needed (optional for now, can use metadata)
            $table->uuid('payment_id')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_passenger_ancillaries');
    }
};
