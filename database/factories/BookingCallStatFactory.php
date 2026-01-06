<?php

namespace Database\Factories;

use App\Models\BookingDataLead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingCallStatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lead_id' => BookingDataLead::factory(),
            'booking_id' => null,
            'outcome' => fake()->randomElement(['answered', 'voicemail', 'no_answer', 'busy', 'failed']),
            'duration' => fake()->numberBetween(0, 1800),
            'notes' => fake()->optional()->sentence(),
            'booked_meeting' => fake()->boolean(20),
            'call_date' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
