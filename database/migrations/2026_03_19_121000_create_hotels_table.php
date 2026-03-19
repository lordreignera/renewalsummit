<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description', 500)->nullable();
            $table->string('image_path')->nullable();
            $table->string('booking_url')->nullable();
            $table->unsignedInteger('single_price_usd')->default(150);
            $table->unsignedInteger('double_price_usd')->default(250);
            $table->unsignedBigInteger('single_price_ugx')->default(550000);
            $table->unsignedBigInteger('double_price_ugx')->default(920000);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('hotels')->insert([
            [
                'name' => 'Speke Resort Munyonyo',
                'slug' => 'speke-resort-munyonyo',
                'description' => 'Luxury lakeside resort on Lake Victoria, close to the summit venue.',
                'image_path' => 'images/hotels/speke-resort.jpg',
                'booking_url' => 'https://www.spekeresort.com',
                'single_price_usd' => 150,
                'double_price_usd' => 250,
                'single_price_ugx' => 550000,
                'double_price_ugx' => 920000,
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Protea Hotel by Marriott',
                'slug' => 'protea-hotel-marriott',
                'description' => 'International-standard hotel with reliable comfort and services.',
                'image_path' => 'images/hotels/protea.jpg',
                'booking_url' => 'https://www.marriott.com/en-us/hotels/ebbka-protea-hotel-kampala/overview/',
                'single_price_usd' => 150,
                'double_price_usd' => 250,
                'single_price_ugx' => 550000,
                'double_price_ugx' => 920000,
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hotel Africana',
                'slug' => 'hotel-africana',
                'description' => 'Popular Kampala hotel with conference-friendly facilities.',
                'image_path' => 'images/hotels/hotel-africana.jpg',
                'booking_url' => 'https://www.hotelafricana.com/web/',
                'single_price_usd' => 150,
                'double_price_usd' => 250,
                'single_price_ugx' => 550000,
                'double_price_ugx' => 920000,
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'St Mbaga Hotel',
                'slug' => 'st-mbaga-hotel',
                'description' => 'Budget-friendly option near Ggaba road and summit access routes.',
                'image_path' => 'images/hotels/st-mbaga.jpg',
                'booking_url' => 'https://www.google.com/search?q=St+Mbaga+Hotel+Kampala',
                'single_price_usd' => 150,
                'double_price_usd' => 250,
                'single_price_ugx' => 550000,
                'double_price_ugx' => 920000,
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
