<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_succeeds_for_active_user(): void
    {
        $user = User::create([
            'name'          => 'Bidan Rina',
            'email'         => 'bidan.rina@prenava.com',
            'password'      => Hash::make('password123'),
            'role'          => 'bidan',
            'is_active'     => true,
            'token_version' => 1,
        ]);

        $res = $this->postJson('/api/auth/login', [
            'email'    => 'bidan.rina@prenava.com',
            'password' => 'password123',
        ]);

        $res->assertStatus(200)->assertJsonStructure([
            'status',
            'user' => ['user_id','name','email','role'],
            'authorization' => ['token','type']
        ]);
    }

    public function test_me_returns_user_with_valid_token(): void
    {
        $user = User::create([
            'name'          => 'Test User',
            'email'         => 'test@ex.com',
            'password'      => Hash::make('password123'),
            'role'          => 'ibu_hamil',
            'is_active'     => true,
            'token_version' => 1,
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email'    => 'test@ex.com',
            'password' => 'password123',
        ], ['Accept' => 'application/json']);

        $loginResponse->assertStatus(200)->assertJsonStructure([
            'authorization' => ['token'],
        ]);

        $token = $loginResponse['authorization']['token'];

        $this->getJson('/api/auth/me', [
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
        ])->assertOk()->assertJson([
            'user_id' => $user->user_id,
            'name'    => $user->name,
            'email'   => $user->email,
            'role'    => $user->role,
        ]);
    }
}
