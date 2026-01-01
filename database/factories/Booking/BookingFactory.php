<?php

namespace Adultdate\FilamentShop\Database\Factories\Booking;

use Adultdate\FilamentShop\Models\Booking\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'number' => 'BK-' . random_int(100000, 999999),
            'shop_client_id' => null,
            'total_price' => $this->faker->randomFloat(2, 10, 500),
            'currency' => 'SEK',
            'status' => 'booked',
            'starts_at' => $this->faker->dateTimeBetween('+1 days', '+30 days'),
            'ends_at' => $this->faker->dateTimeBetween('+31 days', '+60 days'),
            'notes' => $this->faker->sentence(),
        ];
    }
}
