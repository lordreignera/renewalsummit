<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change accommodation_room_type from enum('single','double')
     * to varchar(50) so it can hold any room-type slug (deluxe, twin, family_5pax …).
     */
    public function up(): void
    {
        if (Schema::hasColumn('registrations', 'accommodation_room_type')) {
            DB::statement("ALTER TABLE registrations MODIFY COLUMN accommodation_room_type VARCHAR(50) NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('registrations', 'accommodation_room_type')) {
            // Clamp any value not in the original enum back to 'single' before reverting.
            DB::statement("UPDATE registrations SET accommodation_room_type = 'single'
                           WHERE accommodation_room_type NOT IN ('single','double')
                             AND accommodation_room_type IS NOT NULL");
            DB::statement("ALTER TABLE registrations MODIFY COLUMN accommodation_room_type ENUM('single','double') NULL");
        }
    }
};
