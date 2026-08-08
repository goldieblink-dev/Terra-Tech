<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);

        $this->user = User::factory()->create([
            'email'     => 'admin@terratech.id',
            'password'  => Hash::make('password123'),
            'is_active' => true,
        ]);

        $this->user->assignRole('admin');
    }

    public function test_login_success(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'admin@terratech.id',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login berhasil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'is_active',
                        'roles',
                    ],
                ],
            ]);

        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_login_failure_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'admin@terratech.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'errors' => [
                        'email',
                    ],
                ],
            ]);
    }

    public function test_me_endpoint_returns_authenticated_user(): void
    {
        $token = $this->user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Informasi pengguna berhasil diambil.',
                'data'    => [
                    'id'    => $this->user->id,
                    'email' => 'admin@terratech.id',
                ],
            ]);
    }

    public function test_logout_revokes_token(): void
    {
        $token = $this->user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logout berhasil.',
            ]);

        $this->assertCount(0, $this->user->tokens);
    }

    public function test_unauthenticated_access_returns_401_json(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated.',
                'data'    => null,
            ]);
    }
}
