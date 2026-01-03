<?php

namespace Database\Seeders;

use Adultdate\FilamentBooking\Models\Booking\Order;
use Illuminate\Database\Seeder;

class BookingOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::firstOrCreate(
            ['number' => 'OR-138334'],
            [
                'sort' => 0,
                'booking_customer_id' => 1,
                'status' => 'new',
                'currency' => 'sek',
                'notes' => '<p></p>',
            ]
        );
    }
}
