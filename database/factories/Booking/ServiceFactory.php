<?php

namespace Database\Factories\Booking;

use Adultdate\FilamentBooking\Enums\ServiceStatus;
use Adultdate\FilamentBooking\Models\Booking\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name' => $name = $this->faker->unique()->words(3, true),
            'slug' => Str::slug($name),
            'service_code' => $this->faker->unique()->bothify('SRV-####'),
            'description' => $this->faker->realText(),
            'price' => $this->faker->randomFloat(2, 100, 5000),
            'cost' => $this->faker->randomFloat(2, 50, 2000),
            'is_available' => $this->faker->boolean(),
            'time_duration' => $this->faker->randomElement([30, 60, 90, 120, 180]),
            'status' => $this->faker->randomElement(ServiceStatus::cases())->value,
            'featured' => $this->faker->boolean(),
            'is_visible' => $this->faker->boolean(),
            'published_at' => $this->faker->dateTimeBetween('-1 year', '+1 month'),
            'booking_brand_id' => $this->faker->numberBetween(1, 10),
        ];
    }
}
