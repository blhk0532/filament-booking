<?php

namespace Adultdate\FilamentShop\Models\Shop;

use Database\Factories\Shop\OrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'shop_order_items';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'shop_order_id',
        'shop_product_id',
        'qty',
        'unit_price',
        'sort',
    ];
}
