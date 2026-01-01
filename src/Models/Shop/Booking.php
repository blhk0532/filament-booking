<?php

namespace Adultdate\FilamentShop\Models\Shop;

use Adultdate\FilamentShop\Enums\BookingStatus;
use Adultdate\FilamentShop\Models\Shop\OrderAddress as OrderAddress;
use Database\Factories\Shop\BookingFactory;
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
        return \Adultdate\FilamentShop\Database\Factories\Shop\BookingFactory::new();
    }

    /**
     * Calculate and return the total price from order items
     */
    public function calculateTotalPrice(): float
    {
        return $this->items->sum(function ($item) {
            return $item->qty * $item->unit_price;
        });
    }

    /**
     * Update the total price based on current items
     */
    public function updateTotalPrice(): void
    {
        $this->update(['total_price' => $this->calculateTotalPrice()]);
    }
}
