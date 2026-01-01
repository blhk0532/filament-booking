<?php

namespace Database\Seeders;

use Adultdate\FilamentShop\Models\Shop\OrderAddress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopOrderAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderAddress::firstOrCreate(
            [
                'addressable_type' => 'Adultdate\\FilamentShop\\Models\\Shop\\Order',
                'addressable_id' => 1,
            ],
            [
                'country' => 'Sweden',
                'street' => 'Mölnbovägen 22',
                'city' => 'Järna',
                'zip' => '153 32',
            ]
        );
    }
}
