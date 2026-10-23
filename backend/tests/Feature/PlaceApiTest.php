<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_place(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/places', [
                'name' => 'HeHa Sky View',
                'category' => 'Wisata',
                'rating' => 5,
                'description' => 'Tempat wisata dengan pemandangan yang menarik.',
                'address' => 'Gunungkidul, Yogyakarta',
                'latitude' => -7.849,
                'longitude' => 110.478,
                'visit_date' => '2026-10-20',
            ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'HeHa Sky View');

        $this->assertDatabaseHas('places', [
            'user_id' => $user->id,
            'name' => 'HeHa Sky View',
        ]);
    }
     public function test_guest_cannot_access_places(): void
    {
        $response = $this->getJson('/api/places');

        $response->assertUnauthorized();
    }
}