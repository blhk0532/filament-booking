<?php

namespace Adultdate\FilamentShop\Database\Factories\Booking;

use Adultdate\FilamentShop\Models\Booking\BookingItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookingItem>
 */
class BookingItemFactory extends Factory
{
    protected $model = BookingItem::class;

    public function definition(): array
    {
        return [
            'shop_booking_id' => null,
            'shop_service_id' => null,
            'qty' => 1,
            'unit_price' => $this->faker->randomFloat(2, 50, 200),
            'sort' => 0,
        ];
    }
}
