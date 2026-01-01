<?php

namespace Adultdate\FilamentBooking\Models\Booking;

use Database\Factories\Booking\OrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($item) {
            if ($item->order) {
                $item->order->updateTotalPrice();
            }
        });

        static::deleted(function ($item) {
            if ($item->order) {
                $item->order->updateTotalPrice();
            }
        });
    }

    /**
     * @var string
     */
    protected $table = 'booking_order_items';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'booking_order_id',
        'booking_product_id',
        'qty',
        'unit_price',
        'sort',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'booking_order_id');
    }
}
