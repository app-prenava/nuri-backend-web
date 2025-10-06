<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\JwtTestHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;


class AdminTest extends TestCase
{
    use RefreshDatabase, JwtTestHelpers;

    private function adminHeaders(string $token): array
    {
        return [
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
        ];
    }

    public function test_admin_can_create_bidan_and_dinkes_accounts(): void
    {
        $admin = $this->makeUser(['role' => 'admin']);
        $adminToken = $this->issueToken($admin);

        $this->postJson('/api/admin/create/account/bidan', [
            'name'     => 'Bidan A',
            'email'    => 'bidan.a@ex.com',
            'password' => 'secret123'
        ], $this->adminHeaders($adminToken))
          ->assertStatus(201);

        $this->postJson('/api/admin/create/account/dinkes', [
            'name'     => 'Dinkes A',
            'email'    => 'dinkes.a@ex.com',
            'password' => 'secret123'
        ], $this->adminHeaders($adminToken))
          ->assertStatus(201);
    }

    public function test_admin_deactivate_revokes_existing_tokens(): void
    {
        $admin = $this->makeUser(['role' => 'admin']);
        $adminToken = $this->issueToken($admin);

        $this->postJson('/api/admin/create/account/bidan', [
            'name'     => 'Bidan A',
            'email'    => 'bidanassd1e12@ex.com',
            'password' => 'password123'
        ], $this->adminHeaders($adminToken))
          ->assertStatus(201);

        $bidanLogin = $this->postJson('/api/auth/login', [
            'email'    => 'bidanassd1e12@ex.com',
            'password' => 'password123',
        ])->assertStatus(200);

        $bidanToken = $bidanLogin['authorization']['token'];
        $payload = JWTAuth::setToken($bidanToken)->getPayload();
        $bidan_id = $payload->get('uid');

        $this->getJson('/api/auth/me', [
            'Authorization' => "Bearer {$bidanToken}",
            'Accept'        => 'application/json'
        ])->assertOk();

        $url = "/api/admin/users/{$bidan_id}/deactivate";

        $this->postJson($url, [], $this->adminHeaders($adminToken))
            ->assertOk();

        $this->getJson('/api/auth/me', [
            'Authorization' => "Bearer {$bidanToken}",
            'Accept'        => 'application/json'
        ])->assertStatus(401);
    }

}
