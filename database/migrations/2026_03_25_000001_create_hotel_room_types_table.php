<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('slug', 50);       // machine key: single, double, deluxe, twin, family_5pax …
            $table->string('label', 100);     // display: "Single Room", "Family Room (5 pax)"
            $table->unsignedBigInteger('price_ugx');
            $table->unsignedInteger('price_usd');
            $table->unsignedSmallInteger('room_count')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_room_types');
    }
};
