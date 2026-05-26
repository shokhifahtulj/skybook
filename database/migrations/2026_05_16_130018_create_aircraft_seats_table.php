<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aircraft_seats', function (Blueprint $table) {
            $table->id();
            
            $table->foreignUuid('aircraft_id')
                ->constrained('aircrafts')
                ->cascadeOnDelete();
            
            $table->string('seat_number');
            $table->string('cabin_class')->default('economy');
            
            $table->integer('row_number');
            $table->string('seat_letter');
            
            $table->boolean('is_window')->default(false);
            $table->boolean('is_aisle')->default(false);
            $table->boolean('is_exit_row')->default(false);
            
            $table->timestamps();
            
            $table->unique(['aircraft_id', 'seat_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aircraft_seats');
    }
};
