<?php

namespace Database\Seeders;

use Adultdate\FilamentBooking\Models\Booking\Product;
use Illuminate\Database\Seeder;

class BookingProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::firstOrCreate(
            ['name' => 'IM-Kanal Ventilationsrengöring '],
            [
                'booking_brand_id' => 1,
                'description' => '<p></p>',
                'qty' => 0,
                'security_stock' => 0,
                'featured' => 0,
                'is_visible' => 1,
                'price' => 1800,
                'backorder' => 0,
                'requires_shipping' => 0,
                'published_at' => '2025-12-29 00:00:00',
                'weight_value' => 0,
                'weight_unit' => 'kg',
                'height_value' => 0,
                'height_unit' => 'cm',
                'width_value' => 0,
                'width_unit' => 'cm',
                'depth_value' => 0,
                'depth_unit' => 'cm',
                'volume_value' => 0,
                'volume_unit' => 'l',
            ]
        );
    }
}
