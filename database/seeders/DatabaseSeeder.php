<?php

namespace Database\Seeders;

use App\Models\Postcode;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Postcode::factory()->create([
            'postcode' => 'ST1 1AA'
        ]);

        User::factory()->create([
            'name' => 'Owner',
            'email' => 'store@example.com',
        ])->tokens()->create([
            'name' => 'owner',
            'token' => hash('sha256', '2d4b13702d1a6f35d4fed1b68641230d'),
            'abilities' => ['createStore'],
        ]);


        User::factory()->create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
        ])->tokens()->create([
            'name' => 'customer',
            'token' => hash('sha256', 'f5f132a18d409e4b8284307c4c481487'),
            'abilities' => ['search'],
        ]);
    }
}
