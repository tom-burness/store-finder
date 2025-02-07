<?php

namespace Tests\Feature;

use App\Models\Postcode;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_unauthorized()
    {
        $response = $this->json('GET', '/api/stores', [
            'postcode' => 'SE9 9AF'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_index_returns_store_with_correct_distance_and_order()
    {
        Sanctum::actingAs(User::factory()->create());

        Postcode::factory()->create([
            'postcode' => 'SE9 9AF',
            'lat' => 51.449793,
            'long' => 0.05268,
            'coordinates' => DB::raw("ST_GeomFromText('POINT(51.449793 0.05268)', 4326)"),
        ]);

        // 44.55 KM from the postcode
        $lat1 = 51.050502;
        $long1 = 0.0009;
        Store::factory()->create([
            'name' => 'Test Store 1',
            'status' => 'open',
            'type' => 'takeaway',
            'coordinates' => DB::raw("ST_GeomFromText('POINT($lat1 $long1)', 4326)"),
            'lat' => $lat1,
            'long' => $long1,
        ]);

        // 517.09 KM from the postcode
        $lat2 = 56.1;
        $long2 = 0.0004;
        Store::factory()->create([
            'name' => 'Test Store 2',
            'status' => 'open',
            'type' => 'takeaway',
            'coordinates' => DB::raw("ST_GeomFromText('POINT($lat2 $long2)', 4326)"),
            'lat' => $lat2,
            'long' => $long2,
        ]);

        // 44.43 KM from the postcode
        $lat2 = 51.051502;
        $long2 = 0.0009;
        Store::factory()->create([
            'name' => 'Test Store 3',
            'status' => 'open',
            'type' => 'takeaway',
            'coordinates' => DB::raw("ST_GeomFromText('POINT($lat2 $long2)', 4326)"),
            'lat' => $lat2,
            'long' => $long2,
        ]);

        $response = $this->json('GET', '/api/stores?postcode=SE9 9AF');

        // Assert a successful response and check the structure of the JSON payload.
        $response->assertStatus(200)->assertJson([
            'data' => [
                [
                    'name' => 'Test Store 3',
                    'status' => 'open',
                    'type' => 'takeaway',
                    'distance' => 44
                ],
                [
                    'name' => 'Test Store 1',
                    'status' => 'open',
                    'type' => 'takeaway',
                    'distance' => 45
                ],
                [
                    'name' => 'Test Store 2',
                    'status' => 'open',
                    'type' => 'takeaway',
                    'distance' => 517
                ]
            ],
        ]);
    }
}
