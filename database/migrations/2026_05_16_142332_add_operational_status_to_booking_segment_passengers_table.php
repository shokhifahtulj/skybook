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
        Schema::table('booking_segment_passengers', function (Blueprint $table) {
            $table->string('operational_status')->default('not_checked_in')->after('flight_schedule_seat_id');
            $table->timestamp('checked_in_at')->nullable()->after('operational_status');
            $table->timestamp('boarding_pass_issued_at')->nullable()->after('checked_in_at');
            $table->timestamp('boarded_at')->nullable()->after('boarding_pass_issued_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_segment_passengers', function (Blueprint $table) {
            $table->dropColumn([
                'operational_status',
                'checked_in_at',
                'boarding_pass_issued_at',
                'boarded_at'
            ]);
        });
    }
};
