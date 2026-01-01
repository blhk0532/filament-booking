<?php

namespace Adultdate\FilamentBooking\Database\Seeders;

use Adultdate\FilamentBooking\Models\Booking\BookingLocation;
use Adultdate\FilamentBooking\Models\Booking\BookingSchedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Stockholm - City',
                'code' => 'STO-CITY',
                'address' => 'Drottninggatan 45',
                'city' => 'Stockholm',
                'postal_code' => '111 21',
                'country' => 'Sweden',
                'phone' => '+46 8 123 456',
                'email' => 'stockholm@example.com',
                'description' => 'Main Stockholm location',
                'is_active' => true,
            ],
            [
                'name' => 'Gothenburg - Central',
                'code' => 'GOT-CEN',
                'address' => 'Avenyn 12',
                'city' => 'Gothenburg',
                'postal_code' => '411 36',
                'country' => 'Sweden',
                'phone' => '+46 31 123 456',
                'email' => 'gothenburg@example.com',
                'description' => 'Central Gothenburg location',
                'is_active' => true,
            ],
            [
                'name' => 'Malmö - South',
                'code' => 'MAL-SOU',
                'address' => 'Stortorget 8',
                'city' => 'Malmö',
                'postal_code' => '211 22',
                'country' => 'Sweden',
                'phone' => '+46 40 123 456',
                'email' => 'malmo@example.com',
                'description' => 'Malmö south location',
                'is_active' => true,
            ],
        ];

        foreach ($locations as $locationData) {
            $location = BookingLocation::create($locationData);

            // Create schedules for the next 30 days
            for ($i = 0; $i < 30; $i++) {
                $date = Carbon::now()->addDays($i);

                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }

                BookingSchedule::create([
                    'booking_location_id' => $location->id,
                    'date' => $date->format('Y-m-d'),
                    'start_time' => '08:00:00',
                    'end_time' => '17:00:00',
                    'is_available' => true,
                    'max_bookings' => 10,
                    'notes' => 'Regular business hours',
                ]);
            }
        }
    }
}
