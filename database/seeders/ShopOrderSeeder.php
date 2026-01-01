<?php

namespace Database\Seeders;

use Adultdate\FilamentShop\Models\Shop\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::firstOrCreate(
            ['number' => 'OR-138334'],
            [
                'sort' => 0,
                'shop_customer_id' => 1,
                'status' => 'new',
                'currency' => 'sek',
                'notes' => '<p></p>',
            ]
        );
    }
}
