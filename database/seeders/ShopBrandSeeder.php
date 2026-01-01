<?php

namespace Database\Seeders;

use Adultdate\FilamentBooking\Models\Booking\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Brand::firstOrCreate(
            ['name' => 'Trygg VVS & Energi Nordic AB'],
            [
                'slug' => 'trygg-vvs-energi-nordic-ab',
                'website' => 'https://www.tryggvvs.info/',
                'description' => '<p></p>',
                'position' => 0,
                'is_visible' => 1,
            ]
        );
    }
}
