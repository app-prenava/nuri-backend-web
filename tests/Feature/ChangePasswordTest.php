<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\JwtTestHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase, JwtTestHelpers;

    private function authHeaders(string $token): array
    {
        return [
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
        ];
    }

    public function test_change_password_success_and_old_token_revoked(): void
    {
        $user = User::create([
            'name'          => 'Ibu Hamil',
            'email'         => 'ibu@example.com',
            'password'      => Hash::make('oldpass123'),
            'role'          => 'ibu_hamil',
            'is_active'     => true,
            'token_version' => 1,
        ]);

        $login = $this->postJson('/api/auth/login', [
            'email'    => 'ibu@example.com',
            'password' => 'oldpass123',
        ])->assertStatus(200);

        $oldToken = $login['authorization']['token'];

        $this->putJson('/api/auth/change-password', [
            'new_password' => 'NewPass!234',
        ], $this->authHeaders($oldToken))
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->getJson('/api/auth/me', $this->authHeaders($oldToken))
            ->assertStatus(401);

        $this->postJson('/api/auth/login', [
            'email'    => 'ibu@example.com',
            'password' => 'NewPass!234',
        ])->assertStatus(200);
    }

    public function test_change_password_requires_token(): void
    {
        $this->putJson('/api/auth/change-password', [
            'new_password' => 'NewPass!234',
        ])->assertStatus(401);
    }

    public function test_change_password_validation_error_when_missing_new_password(): void
    {
        $user = User::create([
            'name'          => 'Bidan',
            'email'         => 'bidan@example.com',
            'password'      => Hash::make('secret123'),
            'role'          => 'bidan',
            'is_active'     => true,
            'token_version' => 1,
        ]);

        $token = $this->issueToken($user);

        $this->putJson('/api/auth/change-password', [], $this->authHeaders($token))
            ->assertStatus(422);
    }

    public function test_change_password_bumps_token_version(): void
    {
        $user = User::create([
            'name'          => 'Dinkes',
            'email'         => 'dinkes@example.com',
            'password'      => Hash::make('secret123'),
            'role'          => 'dinkes',
            'is_active'     => true,
            'token_version' => 5,
        ]);

        $token = $this->issueToken($user);

        $this->putJson('/api/auth/change-password', [
            'new_password' => 'Another#123',
        ], $this->authHeaders($token))->assertStatus(200);

        $userFresh = User::where('user_id', $user->user_id)->first();
        $this->assertEquals(6, $userFresh->token_version);
    }
}
