<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\JwtTestHelpers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpiryTest extends TestCase
{
    use RefreshDatabase, JwtTestHelpers;

    public function test_bidan_token_expires_in_1_second_when_configured(): void
    {
        // override config untuk test ini (tanpa edit .env.testing)
        config()->set('auth_tokens.ttl_seconds.bidan', 1);
        config()->set('auth_tokens.ttl_seconds.dinkes', 3600);
        config()->set('auth_tokens.ttl_seconds.default', 0);

        $user = $this->makeUser([
            'role' => 'bidan',
            'email' => 'bidan.12122@test.com',
            'password' => Hash::make('password123'),
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ])->assertStatus(200);

        $token = $loginResponse['authorization']['token'];

        // segera: masih valid
        $this->getJson('/api/auth/me', [
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json'
        ])->assertOk();

        // tunggu >1 detik
        sleep(2);

        // sekarang harus expired
        $this->getJson('/api/auth/me', [
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json'
        ])->assertStatus(401);
    }
}
