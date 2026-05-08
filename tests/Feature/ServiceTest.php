<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_list_can_be_loaded(): void
    {
        $specialist = User::factory()->create([
            'role' => 'SPECIALIST',
        ]);

        Service::create([
            'name' => 'Haircut',
            'duration' => 30,
            'price' => 25.00,
            'specialist_id' => $specialist->id,
        ]);

        $response = $this->getJson('/api/services');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Haircut',
        ]);
    }

    public function test_specialist_can_see_only_their_services(): void
    {
        $specialist = User::factory()->create([
            'role' => 'SPECIALIST',
        ]);

        $otherSpecialist = User::factory()->create([
            'role' => 'SPECIALIST',
        ]);

        Service::create([
            'name' => 'Haircut',
            'duration' => 30,
            'price' => 25.00,
            'specialist_id' => $specialist->id,
        ]);

        Service::create([
            'name' => 'Massage',
            'duration' => 45,
            'price' => 50.00,
            'specialist_id' => $otherSpecialist->id,
        ]);

        $response = $this->actingAs($specialist, 'api')->getJson('/api/my-services');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'name' => 'Haircut',
        ]);

        $response->assertJsonMissing([
            'name' => 'Massage',
        ]);
    }

    public function test_service_requires_name_duration_and_price(): void
    {
        $specialist = User::factory()->create([
            'role' => 'SPECIALIST',
        ]);

        $response = $this->actingAs($specialist)->postJson('/api/services', [
            'name' => '',
            'duration' => '',
            'price' => '',
            'specialist_id' => $specialist->id,
        ]);

        $response->assertStatus(422);
    }

    public function test_specialist_can_create_service(): void
    {
        $specialist = User::factory()->create([
            'role' => 'SPECIALIST',
        ]);

        $response = $this->actingAs($specialist)->postJson('/api/services', [
            'name' => 'Coloring',
            'duration' => 60,
            'price' => 60.00,
            'specialist_id' => $specialist->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('services', [
            'name' => 'Coloring',
            'duration' => 60,
            'price' => 60.00,
            'specialist_id' => $specialist->id,
        ]);
    }
}