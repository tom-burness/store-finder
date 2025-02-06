<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Postcode>
 */
class PostcodeFactory extends Factory
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
            'postcode' => fake()->postcode(),
            'coordinates' => DB::raw("ST_GeomFromText('POINT($lat $long)', 4326)"),
            'lat' => $lat,
            'long' => $long
        ];
    }
}
