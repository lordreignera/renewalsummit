<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('registrations', 'accommodation_hotel_id')) {
                $table->foreignId('accommodation_hotel_id')->nullable()->after('accommodation_choice')->constrained('hotels')->nullOnDelete();
            }

            if (! Schema::hasColumn('registrations', 'accommodation_booking_mode')) {
                $table->enum('accommodation_booking_mode', ['self_book', 'book_through_us_no_payment', 'book_through_us_and_pay'])
                    ->nullable()
                    ->after('accommodation_hotel_id');
            }

            if (! Schema::hasColumn('registrations', 'accommodation_room_type')) {
                $table->enum('accommodation_room_type', ['single', 'double'])->nullable()->after('accommodation_booking_mode');
            }

            if (! Schema::hasColumn('registrations', 'accommodation_nights')) {
                $table->unsignedTinyInteger('accommodation_nights')->default(1)->after('accommodation_room_type');
            }

            if (! Schema::hasColumn('registrations', 'accommodation_currency')) {
                $table->string('accommodation_currency', 3)->nullable()->after('accommodation_nights');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            foreach ([
                'accommodation_currency',
                'accommodation_nights',
                'accommodation_room_type',
                'accommodation_booking_mode',
                'accommodation_hotel_id',
            ] as $column) {
                if (Schema::hasColumn('registrations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
