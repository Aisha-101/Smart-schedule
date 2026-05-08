<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_load_their_appointments(): void
    {
        $client = User::factory()->create([
            'role' => 'CLIENT',
        ]);

        $specialist = User::factory()->create([
            'role' => 'SPECIALIST',
        ]);

        Appointment::create([
            'client_id' => $client->id,
            'specialist_id' => $specialist->id,
            'start_time' => '2026-05-08 12:00:00',
            'end_time' => '2026-05-08 12:30:00',
            'status' => 'SCHEDULED',
        ]);

        $response = $this->actingAs($client)->getJson('/api/appointments/my');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => 'SCHEDULED',
        ]);
    }

    public function test_client_can_create_appointment(): void
    {
        $client = User::factory()->create([
            'role' => 'CLIENT',
        ]);

        $specialist = User::factory()->create([
            'role' => 'SPECIALIST',
        ]);

        $service = Service::create([
            'name' => 'Haircut',
            'duration' => 30,
            'price' => 25.00,
            'specialist_id' => $specialist->id,
        ]);

        $response = $this->actingAs($client)->postJson('/api/appointments', [
            'specialist_id' => $specialist->id,
            'start_time' => '2026-05-08 12:00:00',
            'end_time' => '2026-05-08 12:30:00',
            'services' => [$service->id],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('appointments', [
            'client_id' => $client->id,
            'specialist_id' => $specialist->id,
            'status' => 'SCHEDULED',
        ]);
    }

    public function test_appointment_requires_specialist_and_time(): void
    {
        $client = User::factory()->create([
            'role' => 'CLIENT',
        ]);

        $response = $this->actingAs($client)->postJson('/api/appointments', []);

        $response->assertStatus(422);
    }

    public function test_client_can_cancel_appointment(): void
    {
        $client = User::factory()->create([
            'role' => 'CLIENT',
        ]);

        $specialist = User::factory()->create([
            'role' => 'SPECIALIST',
        ]);

        $appointment = Appointment::create([
            'client_id' => $client->id,
            'specialist_id' => $specialist->id,
            'start_time' => '2026-05-08 12:00:00',
            'end_time' => '2026-05-08 12:30:00',
            'status' => 'SCHEDULED',
        ]);

        $response = $this->actingAs($client)->deleteJson(
            "/api/appointments/{$appointment->id}"
        );

        $response->assertStatus(200);
    }
}