<?php

namespace Database\Seeders;

use Adultdate\FilamentShop\Models\Shop\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::firstOrCreate(
            ['name' => 'VVS'],
            [
                'slug' => 'vvs',
                'description' => '<p></p>',
                'position' => 0,
                'is_visible' => 1,
            ]
        );

        Category::firstOrCreate(
            ['name' => 'Ventilation'],
            [
                'parent_id' => 1,
                'slug' => 'ventilation',
                'description' => '<p></p>',
                'position' => 0,
                'is_visible' => 1,
            ]
        );

        Category::firstOrCreate(
            ['name' => 'Avlopp'],
            [
                'parent_id' => 1,
                'slug' => 'avlopp',
                'description' => '<p></p>',
                'position' => 0,
                'is_visible' => 1,
            ]
        );
    }
}
