<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingDataLeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'luid' => fake()->unique()->regexify('[A-Z0-9]{10}'),
            'name' => fake()->name(),
            'address' => fake()->address(),
            'street' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'zip' => fake()->postcode(),
            'country' => fake()->country(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->optional()->email(),
            'dob' => fake()->optional()->date(),
            'age' => fake()->optional()->numberBetween(18, 80),
            'sex' => fake()->randomElement(['male', 'female', 'other']),
            'status' => fake()->randomElement(['new', 'contacted', 'interested', 'not_interested', 'converted', 'do_not_call']),
            'is_active' => fake()->boolean(80),
            'assigned_to' => User::factory(),
            'attempt_count' => fake()->numberBetween(0, 10),
            'last_contacted_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'notes' => fake()->optional()->paragraph(),
            'metadata' => fake()->optional()->randomElement([
                ['source' => 'web', 'campaign' => fake()->word()],
                ['source' => 'referral', 'referral_code' => fake()->word()],
                null,
            ]),
        ];
    }
}
