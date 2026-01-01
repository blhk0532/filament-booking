<?php

namespace Adultdate\FilamentShop\Models\Booking;

use Database\Factories\Booking\BookingItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    /** @use HasFactory<BookingItemFactory> */
    use HasFactory;

    protected $table = 'shop_booking_items';

    protected $fillable = [
        'shop_booking_id',
        'shop_service_id',
        'qty',
        'unit_price',
        'sort',
    ];

    protected static function newFactory()
    {
        return \Database\Factories\Booking\BookingItemFactory::new();
    }
}
