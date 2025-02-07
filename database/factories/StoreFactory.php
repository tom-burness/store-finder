<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lat = fake()->latitude();
        $long = fake()->longitude();

        return [
            'name' => 'Store 1',
            'status' => 'open',
            'type' => 'grocery',
            'coordinates' => DB::raw("ST_GeomFromText('POINT($lat $long)', 4326)"),
            'lat' => $lat,
            'long' => $long,
            'max_delivery_distance' => fake()->numberBetween(1, 100),
        ];
    }
}
