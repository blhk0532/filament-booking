<?php

namespace Database\Seeders;

use Adultdate\FilamentBooking\Models\Booking\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookingClientSeeder extends Seeder
{
    public function run(): void
    {
        Client::query()->delete();

        $clients = [
            [
                'ulid' => (string) Str::ulid(),
                'name' => 'Anna Andersson',
                'email' => 'anna.andersson@example.com',
                'phone' => '070-1234567',
                'address' => 'Storgatan 12',
                'street' => 'Storgatan 12',
                'city' => 'Stockholm',
                'zip' => '111 22',
                'type' => 'person',
            ],
            [
                'ulid' => (string) Str::ulid(),
                'name' => 'Erik Lindberg',
                'email' => 'erik.lindberg@example.com',
                'phone' => '070-2345678',
                'address' => 'Västra Gatan 45',
                'street' => 'Västra Gatan 45',
                'city' => 'Gothenburg',
                'zip' => '411 21',
                'type' => 'person',
            ],
            [
                'ulid' => (string) Str::ulid(),
                'name' => 'Maria Svensson',
                'email' => 'maria.svensson@example.com',
                'phone' => '070-3456789',
                'address' => 'Kungsgatan 8',
                'street' => 'Kungsgatan 8',
                'city' => 'Malmö',
                'zip' => '211 35',
                'type' => 'person',
            ],
            [
                'ulid' => (string) Str::ulid(),
                'name' => 'Tech Solutions AB',
                'email' => 'info@techsolutions.se',
                'phone' => '031-123456',
                'address' => 'Teknikgatan 15',
                'street' => 'Teknikgatan 15',
                'city' => 'Linköping',
                'zip' => '581 82',
                'type' => 'company',
            ],
            [
                'ulid' => (string) Str::ulid(),
                'name' => 'Lars Johansson',
                'email' => 'lars.johansson@example.com',
                'phone' => '070-4567890',
                'address' => 'Skolgatan 23',
                'street' => 'Skolgatan 23',
                'city' => 'Uppsala',
                'zip' => '752 31',
                'type' => 'person',
            ],
            [
                'ulid' => (string) Str::ulid(),
                'name' => 'Eco Building Systems',
                'email' => 'contact@ecobuildings.se',
                'phone' => '08-543210',
                'address' => 'Miljögatan 67',
                'street' => 'Miljögatan 67',
                'city' => 'Stockholm',
                'zip' => '112 39',
                'type' => 'company',
            ],
            [
                'ulid' => (string) Str::ulid(),
                'name' => 'Sofia Karlsson',
                'email' => 'sofia.karlsson@example.com',
                'phone' => '070-5678901',
                'address' => 'Parkgatan 34',
                'street' => 'Parkgatan 34',
                'city' => 'Västerås',
                'zip' => '721 34',
                'type' => 'person',
            ],
            [
                'ulid' => (string) Str::ulid(),
                'name' => 'Nordic Properties',
                'email' => 'office@nordicprop.se',
                'phone' => '046-289920',
                'address' => 'Stadsgatan 19',
                'street' => 'Stadsgatan 19',
                'city' => 'Växjö',
                'zip' => '351 93',
                'type' => 'company',
            ],
            [
                'ulid' => (string) Str::ulid(),
                'name' => 'Magnus Nilsson',
                'email' => 'magnus.nilsson@example.com',
                'phone' => '070-6789012',
                'address' => 'Brogatan 56',
                'street' => 'Brogatan 56',
                'city' => 'Örebro',
                'zip' => '702 22',
                'type' => 'person',
            ],
            [
                'ulid' => (string) Str::ulid(),
                'name' => 'Green Energy Solutions',
                'email' => 'info@greenenergy.se',
                'phone' => '0920-12345',
                'address' => 'Gröngatan 11',
                'street' => 'Gröngatan 11',
                'city' => 'Helsingborg',
                'zip' => '251 88',
                'type' => 'company',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
