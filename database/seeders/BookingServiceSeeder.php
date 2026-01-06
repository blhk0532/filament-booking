<?php

namespace Database\Seeders;

use Adultdate\FilamentBooking\Enums\ServiceStatus;
use Adultdate\FilamentBooking\Models\Booking\Brand;
use Adultdate\FilamentBooking\Models\Booking\Service;
use Illuminate\Database\Seeder;

class BookingServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::query()->delete();

        // Get all brands to assign to services
        $brands = Brand::all();

        $services = [
            [
                'name' => 'VVS Basic Service',
                'slug' => 'vvs-basic-service',
                'service_code' => 'VVS-BASIC-001',
                'description' => 'Basic VVS inspection and maintenance service',
                'price' => 1500.00,
                'cost' => 800.00,
                'is_available' => true,
                'time_duration' => 60,
                'status' => ServiceStatus::Booked->value,
                'featured' => true,
                'is_visible' => true,
                'published_at' => now()->subMonth(),
                'booking_brand_id' => $brands->get(0)?->id ?? 1,
            ],
            [
                'name' => 'Ventilation System Installation',
                'slug' => 'ventilation-system-installation',
                'service_code' => 'VENT-INST-001',
                'description' => 'Complete ventilation system installation for residential properties',
                'price' => 8500.00,
                'cost' => 4500.00,
                'is_available' => true,
                'time_duration' => 180,
                'status' => ServiceStatus::Booked->value,
                'featured' => true,
                'is_visible' => true,
                'published_at' => now()->subWeeks(2),
                'booking_brand_id' => $brands->get(1)?->id ?? 2,
            ],
            [
                'name' => 'Heat Pump Maintenance',
                'slug' => 'heat-pump-maintenance',
                'service_code' => 'HEAT-MAINT-001',
                'description' => 'Annual heat pump maintenance and efficiency check',
                'price' => 2200.00,
                'cost' => 1200.00,
                'is_available' => true,
                'time_duration' => 90,
                'status' => ServiceStatus::Booked->value,
                'featured' => false,
                'is_visible' => true,
                'published_at' => now()->subDays(5),
                'booking_brand_id' => $brands->get(2)?->id ?? 3,
            ],
            [
                'name' => 'Air Quality Assessment',
                'slug' => 'air-quality-assessment',
                'service_code' => 'AIR-QUAL-001',
                'description' => 'Comprehensive indoor air quality testing and recommendations',
                'price' => 3500.00,
                'cost' => 1800.00,
                'is_available' => true,
                'time_duration' => 120,
                'status' => ServiceStatus::Booked->value,
                'featured' => true,
                'is_visible' => true,
                'published_at' => now()->subWeek(),
                'booking_brand_id' => $brands->get(3)?->id ?? 4,
            ],
            [
                'name' => 'Radiant Heating Installation',
                'slug' => 'radiant-heating-installation',
                'service_code' => 'RAD-INST-001',
                'description' => 'Professional radiat heating system installation',
                'price' => 12000.00,
                'cost' => 6500.00,
                'is_available' => true,
                'time_duration' => 180,
                'status' => ServiceStatus::Processing->value,
                'featured' => false,
                'is_visible' => true,
                'published_at' => now()->subWeeks(3),
                'booking_brand_id' => $brands->get(4)?->id ?? 5,
            ],
            [
                'name' => 'HVAC System Design',
                'slug' => 'hvac-system-design',
                'service_code' => 'HVAC-DSGN-001',
                'description' => 'Custom HVAC system design for new constructions',
                'price' => 5500.00,
                'cost' => 3000.00,
                'is_available' => true,
                'time_duration' => 90,
                'status' => ServiceStatus::Booked->value,
                'featured' => false,
                'is_visible' => true,
                'published_at' => now()->subDays(10),
                'booking_brand_id' => $brands->get(5)?->id ?? 6,
            ],
            [
                'name' => 'Duct Cleaning Service',
                'slug' => 'duct-cleaning-service',
                'service_code' => 'DUCT-CLN-001',
                'description' => 'Complete duct cleaning and sanitization',
                'price' => 4200.00,
                'cost' => 2200.00,
                'is_available' => true,
                'time_duration' => 120,
                'status' => ServiceStatus::Booked->value,
                'featured' => true,
                'is_visible' => true,
                'published_at' => now()->subDays(3),
                'booking_brand_id' => $brands->get(6)?->id ?? 7,
            ],
            [
                'name' => 'Energy Audit Service',
                'slug' => 'energy-audit-service',
                'service_code' => 'ENGY-AUD-001',
                'description' => 'Complete energy efficiency audit and recommendations',
                'price' => 6500.00,
                'cost' => 3500.00,
                'is_available' => true,
                'time_duration' => 150,
                'status' => ServiceStatus::Confirmed->value,
                'featured' => false,
                'is_visible' => true,
                'published_at' => now()->subWeeks(4),
                'booking_brand_id' => $brands->get(7)?->id ?? 8,
            ],
            [
                'name' => 'Boiler Service & Repair',
                'slug' => 'boiler-service-repair',
                'service_code' => 'BOILER-SRV-001',
                'description' => 'Boiler maintenance, service, and emergency repairs',
                'price' => 2800.00,
                'cost' => 1500.00,
                'is_available' => true,
                'time_duration' => 60,
                'status' => ServiceStatus::Booked->value,
                'featured' => true,
                'is_visible' => true,
                'published_at' => now()->subDays(7),
                'booking_brand_id' => $brands->get(8)?->id ?? 9,
            ],
            [
                'name' => 'Smart Thermostat Installation',
                'slug' => 'smart-thermostat-installation',
                'service_code' => 'SMART-THERM-001',
                'description' => 'Installation of smart thermostat with home automation integration',
                'price' => 4500.00,
                'cost' => 2400.00,
                'is_available' => true,
                'time_duration' => 90,
                'status' => ServiceStatus::Booked->value,
                'featured' => false,
                'is_visible' => true,
                'published_at' => now()->subDays(2),
                'booking_brand_id' => $brands->get(9)?->id ?? 10,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
