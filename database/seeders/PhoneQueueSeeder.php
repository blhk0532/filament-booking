<?php

namespace Database\Seeders;

use Adultdate\FilamentShop\PhoneQueue;
use Illuminate\Database\Seeder;

class PhoneQueueSeeder extends Seeder
{
    public function run(): void
    {
        PhoneQueue::factory()->count(5)->create();
    }
}
