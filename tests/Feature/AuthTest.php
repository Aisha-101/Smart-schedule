<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'name' => 'Test Client',
            'email' => 'user@test.lt',
            'password' => Hash::make('password123'),
            'role' => 'CLIENT',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@test.lt',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'user@test.lt',
            'password' => Hash::make('password123'),
            'role' => 'CLIENT',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@test.lt',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(401);
    }
}