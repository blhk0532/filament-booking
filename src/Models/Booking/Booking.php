<?php

namespace Adultdate\FilamentShop\Models\Booking;

use Adultdate\FilamentShop\Enums\BookingStatus;
use Adultdate\FilamentShop\Models\Booking\OrderAddress as OrderAddress;
use Database\Factories\Booking\BookingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $table = 'shop_bookings';

    protected $fillable = [
        'number',
        'shop_client_id',
        'total_price',
        'currency',
        'status',
        'starts_at',
        'ends_at',
        'notes',
    ];

    protected $casts = [
        'status' => BookingStatus::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /** @return MorphOne<OrderAddress, $this> */
    public function address(): MorphOne
    {
        return $this->morphOne(OrderAddress::class, 'addressable');
    }

    /** @return BelongsTo<Client, $this> */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'shop_client_id');
    }

    /** @return HasMany<BookingItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class, 'shop_booking_id');
    }

    protected static function newFactory()
    {
        return \Adultdate\FilamentShop\Database\Factories\Booking\BookingFactory::new();
    }
}
