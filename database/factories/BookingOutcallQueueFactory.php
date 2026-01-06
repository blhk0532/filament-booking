<?php

namespace Database\Factories;

use App\Models\BookingOutcallQueue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingOutcallQueueFactory extends Factory
{
    protected $model = BookingOutcallQueue::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 week', '+1 week');

        return [
            'luid' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'address' => $this->faker->streetAddress(),
            'street' => $this->faker->streetName(),
            'city' => $this->faker->city(),
            'maps' => null,
            'age' => $this->faker->numberBetween(18, 60),
            'sex' => $this->faker->randomElement(['M', 'F']),
            'dob' => $this->faker->date(),
            'phone' => $this->faker->phoneNumber(),
            'status' => 'pending',
            'type' => 'outcall',
            'notes' => $this->faker->sentence(),
            'result' => null,
            'attempts' => 0,
            'user_id' => User::factory(),
            'service_user_id' => null,
            'booking_user_id' => null,
            'start_time' => $start,
            'end_time' => (clone $start)->modify('+1 hour'),
            'is_active' => true,
        ];
    }
}
