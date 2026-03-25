<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('hotel_room_types')->delete();
        DB::table('hotels')->truncate();

        // USD rate is pulled from app config (env USD_TO_UGX_RATE, default 3700).
        // To change the rate: update USD_TO_UGX_RATE in .env and re-run this seeder.
        $rate = (int) config('app.usd_ugx_rate', 3700);

        $hotels = [
            [
                'name'        => 'Sir Jose Hotel',
                'slug'        => 'sir-jose-hotel',
                'description' => 'Singles 170k, Doubles 247k, Deluxe 285k, Twins 300k, Family (5pax) 380k per night.',
                'image_path'  => 'images/hotels/sir-jose.jpg',
                'booking_url' => null,
                'sort_order'  => 1,
                'room_types'  => [
                    ['slug' => 'single',      'label' => 'Single Room',         'price_ugx' => 170000, 'room_count' => 16],
                    ['slug' => 'double',      'label' => 'Double Room',         'price_ugx' => 247000, 'room_count' =>  8],
                    ['slug' => 'deluxe',      'label' => 'Deluxe Room',         'price_ugx' => 285000, 'room_count' =>  2],
                    ['slug' => 'twin',        'label' => 'Twin Room',           'price_ugx' => 300000, 'room_count' =>  2],
                    ['slug' => 'family_5pax', 'label' => 'Family Room (5 pax)', 'price_ugx' => 380000, 'room_count' =>  1],
                ],
            ],
            [
                'name'        => 'Grace Land Hotel',
                'slug'        => 'grace-land-hotel',
                'description' => '60 rooms at 65,000 UGX per night.',
                'image_path'  => 'images/hotels/grace-land.jpg',
                'booking_url' => null,
                'sort_order'  => 2,
                'room_types'  => [
                    ['slug' => 'standard', 'label' => 'Standard Room', 'price_ugx' => 65000, 'room_count' => 60],
                ],
            ],
            [
                'name'        => '611 Hotel',
                'slug'        => '611-hotel',
                'description' => 'Singles 120k, Doubles 150k, Twins 200k, Family (3pax) 500k per night.',
                'image_path'  => 'images/hotels/611-hotel.jpg',
                'booking_url' => null,
                'sort_order'  => 3,
                'room_types'  => [
                    ['slug' => 'single',      'label' => 'Single Room',         'price_ugx' => 120000, 'room_count' => 12],
                    ['slug' => 'double',      'label' => 'Double Room',         'price_ugx' => 150000, 'room_count' => 11],
                    ['slug' => 'twin',        'label' => 'Twin Room',           'price_ugx' => 200000, 'room_count' =>  2],
                    ['slug' => 'family_3pax', 'label' => 'Family Room (3 pax)', 'price_ugx' => 500000, 'room_count' =>  2],
                ],
            ],
            [
                'name'        => 'Victoria Hotel',
                'slug'        => 'victoria-hotel',
                'description' => 'Singles 115k, Doubles 150k, Deluxe 200k, Twin 180k, Triple 230k per night.',
                'image_path'  => 'images/hotels/victoria-hotel.jpg',
                'booking_url' => null,
                'sort_order'  => 4,
                'room_types'  => [
                    ['slug' => 'single', 'label' => 'Single Room', 'price_ugx' => 115000, 'room_count' =>  5],
                    ['slug' => 'double', 'label' => 'Double Room', 'price_ugx' => 150000, 'room_count' =>  8],
                    ['slug' => 'deluxe', 'label' => 'Deluxe Room', 'price_ugx' => 200000, 'room_count' =>  8],
                    ['slug' => 'twin',   'label' => 'Twin Room',   'price_ugx' => 180000, 'room_count' =>  1],
                    ['slug' => 'triple', 'label' => 'Triple Room', 'price_ugx' => 230000, 'room_count' =>  1],
                ],
            ],
            [
                'name'        => 'Eka Hotel',
                'slug'        => 'eka-hotel',
                'description' => 'Singles 140k, Doubles 230k, 3-Bed Room 660k, Bed only 550k per night.',
                'image_path'  => 'images/hotels/eka-hotel.jpg',
                'booking_url' => null,
                'sort_order'  => 5,
                'room_types'  => [
                    ['slug' => 'single',    'label' => 'Single Room', 'price_ugx' => 140000, 'room_count' => 2],
                    ['slug' => 'double',    'label' => 'Double Room', 'price_ugx' => 230000, 'room_count' => 8],
                    ['slug' => 'bed_3room', 'label' => '3-Bed Room',  'price_ugx' => 660000, 'room_count' => 1],
                    ['slug' => 'bed_only',  'label' => 'Bed Only',    'price_ugx' => 550000, 'room_count' => 0],
                ],
            ],
            [
                'name'        => 'Olympia Hotel',
                'slug'        => 'olympia-hotel',
                'description' => 'Singles 80k, Doubles 100k per night.',
                'image_path'  => 'images/hotels/olympia-hotel.jpg',
                'booking_url' => null,
                'sort_order'  => 6,
                'room_types'  => [
                    ['slug' => 'single', 'label' => 'Single Room', 'price_ugx' =>  80000, 'room_count' => 20],
                    ['slug' => 'double', 'label' => 'Double Room', 'price_ugx' => 100000, 'room_count' => 10],
                ],
            ],
            [
                'name'        => 'St Mary Hotel',
                'slug'        => 'st-mary-hotel',
                'description' => 'Singles 45k (208 rooms) and 65k (48 rooms) per night.',
                'image_path'  => 'images/hotels/st-mary.jpg',
                'booking_url' => null,
                'sort_order'  => 7,
                'room_types'  => [
                    ['slug' => 'standard_a', 'label' => 'Single Room (45k)',  'price_ugx' => 45000, 'room_count' => 208],
                    ['slug' => 'standard_b', 'label' => 'Single Room (65k)',  'price_ugx' => 65000, 'room_count' =>  48],
                ],
            ],
            [
                'name'        => 'St Mbaga Hotel',
                'slug'        => 'st-mbaga-hotel',
                'description' => '150 rooms at 45,000 UGX per night.',
                'image_path'  => 'images/hotels/st-mbaga.jpg',
                'booking_url' => null,
                'sort_order'  => 8,
                'room_types'  => [
                    ['slug' => 'standard', 'label' => 'Standard Room', 'price_ugx' => 45000, 'room_count' => 150],
                ],
            ],
        ];

        foreach ($hotels as $h) {
            $singleUgx = collect($h['room_types'])->firstWhere('slug', 'single')['price_ugx']
                ?? $h['room_types'][0]['price_ugx'];
            $doubleUgx = collect($h['room_types'])->firstWhere('slug', 'double')['price_ugx']
                ?? $h['room_types'][0]['price_ugx'];

            $hotelId = DB::table('hotels')->insertGetId([
                'name'             => $h['name'],
                'slug'             => $h['slug'],
                'description'      => $h['description'],
                'image_path'       => $h['image_path'],
                'booking_url'      => $h['booking_url'],
                'single_price_ugx' => $singleUgx,
                'double_price_ugx' => $doubleUgx,
                'single_price_usd' => (int) round($singleUgx / $rate),
                'double_price_usd' => (int) round($doubleUgx / $rate),
                'sort_order'       => $h['sort_order'],
                'is_active'        => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            foreach ($h['room_types'] as $i => $rt) {
                DB::table('hotel_room_types')->insert([
                    'hotel_id'   => $hotelId,
                    'slug'       => $rt['slug'],
                    'label'      => $rt['label'],
                    'price_ugx'  => $rt['price_ugx'],
                    'price_usd'  => (int) round($rt['price_ugx'] / $rate),
                    'room_count' => $rt['room_count'],
                    'sort_order' => $i + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
