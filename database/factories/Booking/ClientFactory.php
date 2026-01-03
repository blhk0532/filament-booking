<?php

namespace Adultdate\FilamentBooking\Database\Factories\Booking;

use Adultdate\FilamentBooking\Models\Booking\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Adultdate\FilamentBooking\Models\Booking\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ulid' => (string) Str::ulid(),
            'name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'birthday' => $this->faker->date(),
            'photo' => $this->faker->imageUrl(),
            'notes' => $this->faker->sentence(),
            'type' => 'person',
        ];
    }
}
