<?php

namespace Database\Seeders;

use Adultdate\FilamentBooking\Models\Booking\Brand;
use Illuminate\Database\Seeder;

class BookingBrandSeeder extends Seeder
{
    public function run(): void
    {
        Brand::query()->delete();

        $brands = [
            [
                'name' => 'Trygg VVS & Energi Nordic AB',
                'slug' => 'trygg-vvs-energi-nordic-ab',
                'website' => 'https://www.tryggvvs.info/',
                'description' => 'Professional HVAC and ventilation services',
                'position' => 1,
                'is_visible' => true,
            ],
            [
                'name' => 'Högberg & Sons AB',
                'slug' => 'hogberg-sons-ab',
                'website' => 'https://www.hogbergsons.se/',
                'description' => 'Leading provider of heating solutions',
                'position' => 2,
                'is_visible' => true,
            ],
            [
                'name' => 'Ventilation Tech Sweden',
                'slug' => 'ventilation-tech-sweden',
                'website' => 'https://www.ventilationtech.se/',
                'description' => 'Advanced ventilation systems',
                'position' => 3,
                'is_visible' => true,
            ],
            [
                'name' => 'EcoHeat Solutions',
                'slug' => 'ecoheat-solutions',
                'website' => 'https://www.ecoheat.se/',
                'description' => 'Eco-friendly heating systems',
                'position' => 4,
                'is_visible' => true,
            ],
            [
                'name' => 'Scandinavian Climate Control',
                'slug' => 'scandinavian-climate-control',
                'website' => 'https://www.scc-nordic.com/',
                'description' => 'Complete climate control solutions',
                'position' => 5,
                'is_visible' => true,
            ],
            [
                'name' => 'Air Quality Systems',
                'slug' => 'air-quality-systems',
                'website' => 'https://www.airquality.se/',
                'description' => 'Indoor air quality specialists',
                'position' => 6,
                'is_visible' => true,
            ],
            [
                'name' => 'Nordic Heating AB',
                'slug' => 'nordic-heating-ab',
                'website' => 'https://www.nordicheating.se/',
                'description' => 'Modern heating technologies',
                'position' => 7,
                'is_visible' => true,
            ],
            [
                'name' => 'SmartVent Sweden',
                'slug' => 'smartvent-sweden',
                'website' => 'https://www.smartvent.se/',
                'description' => 'Smart ventilation systems',
                'position' => 8,
                'is_visible' => true,
            ],
            [
                'name' => 'Thermal Comfort Group',
                'slug' => 'thermal-comfort-group',
                'website' => 'https://www.thermalcomfort.se/',
                'description' => 'Thermal comfort solutions',
                'position' => 9,
                'is_visible' => true,
            ],
            [
                'name' => 'HVAC Masters Sweden',
                'slug' => 'hvac-masters-sweden',
                'website' => 'https://www.hvacmasters.se/',
                'description' => 'HVAC installation and maintenance',
                'position' => 10,
                'is_visible' => true,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
