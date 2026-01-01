<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopCategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shop_category_product')->insertOrIgnore([
            [
                'shop_category_id' => 2,
                'shop_product_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shop_category_id' => 1,
                'shop_product_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
