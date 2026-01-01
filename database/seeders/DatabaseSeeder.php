<?php

declare(strict_types=1);

namespace Database\Seeders;
use Adultdate\FilamentShop\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@ndsth.com'],
            [
                'name' => 'admin',
                'password' => 'bkkbkk',
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            ShopBrandSeeder::class,
            ShopCategorySeeder::class,
            ShopCategoryProductSeeder::class,
            ShopProductSeeder::class,
            ShopCustomerSeeder::class,
            ShopOrderSeeder::class,
            ShopOrderAddressSeeder::class,
        ]);
    }
}
