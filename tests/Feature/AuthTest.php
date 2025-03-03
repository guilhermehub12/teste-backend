<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\Api\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);
    }

    public function test_user_can_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'telefone' => '11999999999',
            'is_valid' => true,
            'is_admin' => false
        ];

        $response = $this->postJson('/auth/register', $userData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'telefone',
                    'is_valid',
                    'is_admin'
                ],
                'token'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'Test User'
        ]);
    }

    public function test_user_can_login()
    {
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/auth/login', $credentials);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email'
                ],
                'token'
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/auth/login', $credentials);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Credenciais inválidas'
            ]);
    }

    public function test_user_can_logout()
    {
        Passport::actingAs($this->user);

        $response = $this->postJson('/auth/logout');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Logout realizado com sucesso'
            ]);
    }

    public function test_user_can_get_profile()
    {
        Passport::actingAs($this->user);

        $response = $this->getJson('/auth/profile');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'telefone',
                    'is_valid',
                    'is_admin'
                ]
            ]);
    }

    public function test_unauthenticated_user_cannot_access_profile()
    {
        $response = $this->getJson('/auth/profile');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
