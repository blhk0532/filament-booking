<?php

namespace Database\Seeders;

use Adultdate\FilamentBooking\Models\Booking\OrderAddress;
use Illuminate\Database\Seeder;

class BookingOrderAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderAddress::firstOrCreate(
            [
                'addressable_type' => 'Adultdate\\FilamentBooking\\Models\\Booking\\Order',
                'addressable_id' => 1,
            ],
            [
                'country' => 'Sweden',
                'street' => 'Mölnbovägen 22',
                'city' => 'Järna',
                'zip' => '153 32',
            ]
        );
    }
}
