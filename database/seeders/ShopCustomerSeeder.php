<?php

namespace Database\Seeders;

use Adultdate\FilamentBooking\Models\Booking\Customer;
use Illuminate\Database\Seeder;

class BookingCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::firstOrCreate(
            ['email' => 'thomasdenk@example.com'],
            [
                'ulid' => '01KDNVG8C6A5KZVQDJKRD68D3G',
                'name' => 'Thomas Denk',
                'address' => 'Mölnbovägen 22 153 32 Järna',
                'phone' => '070-2254197',
                'type' => 'person',
            ]
        );
    }
}
