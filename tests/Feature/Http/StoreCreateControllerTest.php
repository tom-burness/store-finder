<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreCreateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_returns_unauthorized_user_no_user()
    {
        $response = $this->json('POST', '/api/stores', [
            'postcode' => 'SE9 9AF'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_store_returns_forbidden_user_incorrect_permissions()
    {
        Sanctum::actingAs(User::factory()->create(), ['search']);

        $validData = $this->validData();
        $response = $this->json('POST', '/api/stores', $validData);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You cannot create a store.',
            ]);
    }

    public function test_store_can_be_created()
    {
        Sanctum::actingAs(User::factory()->create(), ['createStore']);

        $validData = $this->validData();
        $response = $this->json('POST', '/api/stores', $validData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => $validData['name'],
                    'status' => $validData['status'],
                    'type' => $validData['type'],
                ]
            ]);

        $this->assertDatabaseHas('stores', [
            'name' => $validData['name'],
            'type' => $validData['type'],
            'status' => $validData['status'],
            'lat' => $validData['latitude'],
            'long' => $validData['longitude'],
            'type' => $validData['type'],
            'max_delivery_distance' => $validData['max_delivery_distance'],
        ]);
    }

    public function test_index_returns_validation_error()
    {
        Sanctum::actingAs(User::factory()->create(), ['createStore']);

        $response = $this->json('POST', '/api/stores');

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => ['The store name is required.'],
                    'type' => ['The store type is required.'],
                    'status' => ['The store status is required.'],
                    'latitude' => ['Latitude is required.'],
                    'longitude' => ['Longitude is required.'],
                    'max_delivery_distance' => ['Max distance is required.'],
                ]

            ]);

        $this->assertDatabaseEmpty('stores');
    }

    private function validData(): array
    {
        return [
            'name' => 'Store Name',
            'status' => 'open',
            'type' => 'takeaway',
            'latitude' => 51.449793,
            'longitude' => 0.05268,
            'max_delivery_distance' => 50,
        ];
    }
}
