<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();
            
            $table->string('payment_reference')->unique();
            $table->string('gateway')->default('mock');
            
            $table->decimal('amount', 15, 2);
            $table->string('currency')->default('IDR');
            
            $table->string('status')->default('pending');
            
            $table->timestamp('paid_at')->nullable();
            
            $table->json('raw_payload')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
