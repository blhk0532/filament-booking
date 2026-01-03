<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingCategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('booking_category_product')->insertOrIgnore([
            [
                'booking_category_id' => 2,
                'booking_product_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'booking_category_id' => 1,
                'booking_product_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
