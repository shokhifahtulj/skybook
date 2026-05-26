<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_schedule_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('flight_schedule_id')->constrained('flight_schedules')->cascadeOnDelete();
            
            $table->enum('cabin_class', [
                'economy',
                'premium_economy',
                'business',
                'first'
            ]);
            
            $table->decimal('price', 15, 2);
            $table->integer('quota');
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            
            $table->unique(['flight_schedule_id', 'cabin_class']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_schedule_prices');
    }
};
