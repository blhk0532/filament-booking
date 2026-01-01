<?php

namespace Adultdate\FilamentBooking\Models\Booking;

use Adultdate\FilamentBooking\Database\Factories\Booking\BookingItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItem extends Model
{
    /** @use HasFactory<BookingItemFactory> */
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($item) {
            if ($item->booking) {
                $item->booking->updateTotalPrice();
            }
        });

        static::deleted(function ($item) {
            if ($item->booking) {
                $item->booking->updateTotalPrice();
            }
        });
    }

    protected $table = 'booking_booking_items';

    protected $fillable = [
        'booking_booking_id',
        'booking_service_id',
        'qty',
        'unit_price',
        'sort',
    ];

    /** @return BelongsTo<Booking, $this> */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_booking_id');
    }

    /** @return BelongsTo<Service, $this> */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'booking_service_id');
    }

    protected static function newFactory()
    {
        return BookingItemFactory::new();
    }
}
