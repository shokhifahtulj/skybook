<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->string('pnr', 6)->unique();
            $table->string('booking_status')->default('draft');
            $table->string('payment_status')->default('unpaid');
            
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('currency')->default('IDR');
            
            $table->foreignId('booked_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamp('expires_at')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
