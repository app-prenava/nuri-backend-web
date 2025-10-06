<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

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

    private function issueToken(User $user): string
    {
        return JWTAuth::claims([
            'uid'   => $user->user_id,
            'role'  => $user->role,
            'name'  => $user->name,
            'email' => $user->email,
            'tv'    => (int) $user->token_version,
        ])->fromUser($user);
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
        ], [
            'Accept' => 'application/json',
        ]);

        $loginResponse->assertStatus(200)->assertJsonStructure([
            'authorization' => ['token'],
        ]);

        $token = $loginResponse['authorization']['token'];

        $this->getJson('/api/auth/me', [
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
        ])
        ->assertOk()
        ->assertJson([
            'user_id' => $user->user_id,
            'name'    => $user->name,
            'email'   => $user->email,
            'role'    => $user->role,
        ]);
    }
}
