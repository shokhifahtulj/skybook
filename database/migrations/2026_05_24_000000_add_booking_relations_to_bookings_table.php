<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('bookings', 'schedule_id')) {
                $table->foreignId('schedule_id')->nullable()->after('user_id')->constrained('schedules')->nullOnDelete();
            }

            if (! Schema::hasColumn('bookings', 'flight_id')) {
                $table->uuid('flight_id')->nullable()->after('schedule_id');
            }

            if (! Schema::hasColumn('bookings', 'jumlah_tiket')) {
                $table->unsignedInteger('jumlah_tiket')->default(1)->after('flight_id');
            }

            if (! Schema::hasColumn('bookings', 'total_harga')) {
                $table->decimal('total_harga', 15, 2)->default(0)->after('jumlah_tiket');
            }

            if (! Schema::hasColumn('bookings', 'status_booking')) {
                $table->string('status_booking')->default('confirmed')->after('total_harga');
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'flight_id')) {
                $table->foreign('flight_id')->references('id')->on('flights')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'flight_id')) {
                $table->dropForeign(['flight_id']);
            }

            if (Schema::hasColumn('bookings', 'status_booking')) {
                $table->dropColumn('status_booking');
            }

            if (Schema::hasColumn('bookings', 'total_harga')) {
                $table->dropColumn('total_harga');
            }

            if (Schema::hasColumn('bookings', 'jumlah_tiket')) {
                $table->dropColumn('jumlah_tiket');
            }

            if (Schema::hasColumn('bookings', 'flight_id')) {
                $table->dropColumn('flight_id');
            }

            if (Schema::hasColumn('bookings', 'schedule_id')) {
                $table->dropForeign(['schedule_id']);
                $table->dropColumn('schedule_id');
            }

            if (Schema::hasColumn('bookings', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
