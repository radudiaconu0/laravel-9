<?php

namespace Tests\Feature;



use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // Registration Test
    // Registration Test
    public function test_registration_works_as_expected()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ]);
        $response->assertStatus(201);
        $response->assertJson(['message' => 'User registered']);
        $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);
    }

// Login Test
    public function test_login_works_as_expected()
    {
        $user = User::factory()->create([
            'email' => 'johndoe@example.com',
            'password' => bcrypt('secret123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'johndoe@example.com',
            'password' => 'secret123'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

// Fetch Authenticated User Data Test
    public function test_fetch_authenticated_user_data()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

// Logout Test
    public function test_logout_works_as_expected()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
        $response = $this->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Logged out']);
        // Ensure token is revoked
        $this->assertCount(0, $user->tokens);
    }
}
