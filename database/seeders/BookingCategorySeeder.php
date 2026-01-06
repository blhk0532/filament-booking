<?php

namespace Database\Seeders;

use Adultdate\FilamentBooking\Models\Booking\Category;
use Illuminate\Database\Seeder;

class BookingCategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::query()->delete();

        // Create parent category first
        $parentCategory = Category::create([
            'name' => 'VVS',
            'slug' => 'vvs',
            'description' => 'General VVS services including heating, ventilation, and sanitation',
            'position' => 1,
            'is_visible' => true,
        ]);

        // Now create child categories with the actual parent ID
        $childCategories = [
            [
                'name' => 'Ventilation',
                'slug' => 'ventilation',
                'parent_id' => $parentCategory->id,
                'description' => 'Ventilation system installation and maintenance',
                'position' => 1,
                'is_visible' => true,
            ],
            [
                'name' => 'Värme',
                'slug' => 'varme',
                'parent_id' => $parentCategory->id,
                'description' => 'Heating system services',
                'position' => 2,
                'is_visible' => true,
            ],
            [
                'name' => 'Avlopp',
                'slug' => 'avlopp',
                'parent_id' => $parentCategory->id,
                'description' => 'Drainage and plumbing services',
                'position' => 3,
                'is_visible' => true,
            ],
            [
                'name' => 'Kyla',
                'slug' => 'kyla',
                'parent_id' => $parentCategory->id,
                'description' => 'Cooling and air conditioning services',
                'position' => 4,
                'is_visible' => true,
            ],
            [
                'name' => 'Vatten',
                'slug' => 'vatten',
                'parent_id' => $parentCategory->id,
                'description' => 'Water and sanitation systems',
                'position' => 5,
                'is_visible' => true,
            ],
            [
                'name' => 'Sotning',
                'slug' => 'sotning',
                'parent_id' => $parentCategory->id,
                'description' => 'Chimney and flue services',
                'position' => 6,
                'is_visible' => true,
            ],
            [
                'name' => 'Energi',
                'slug' => 'energi',
                'parent_id' => $parentCategory->id,
                'description' => 'Energy efficiency and renewable solutions',
                'position' => 7,
                'is_visible' => true,
            ],
            [
                'name' => 'Service',
                'slug' => 'service',
                'parent_id' => $parentCategory->id,
                'description' => 'Maintenance and service contracts',
                'position' => 8,
                'is_visible' => true,
            ],
            [
                'name' => 'Installation',
                'slug' => 'installation',
                'parent_id' => $parentCategory->id,
                'description' => 'Complete installation services',
                'position' => 9,
                'is_visible' => true,
            ],
        ];

        foreach ($childCategories as $category) {
            Category::create($category);
        }
    }
}
