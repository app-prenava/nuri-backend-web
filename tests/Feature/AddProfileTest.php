<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\JwtTestHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AddProfileTest extends TestCase
{
    use RefreshDatabase, JwtTestHelpers;

    public function test_create_ibu_hamil_profile_succeeds_once_and_rejects_duplicate(): void
    {
        $user = $this->makeUser([
            'role' => 'ibu_hamil',
            'email' => 'ibu.hamil@test.com',
            'password' => Hash::make('password123'),
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ])->assertStatus(200);

        $token = $loginResponse['authorization']['token'];

        $this->postJson('/api/profile', [
            'tanggal_lahir'       => '1995-03-15',
            'usia'                => 29,
            'alamat'              => 'Jl. Melati',
            'no_telepon'          => '08123',
            'pendidikan_terakhir' => 'SMA',
            'pekerjaan'           => 'Guru',
            'golongan_darah'      => 'O'
        ], [
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json'
        ])->assertStatus(201);

        $this->postJson('/api/profile', [
            'tanggal_lahir' => '1995-03-15',
        ], [
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json'
        ])->assertStatus(409);
    }

    public function test_update_ibu_hamil_profile_requires_existing_row(): void
    {
        $user = $this->makeUser([
            'role' => 'ibu_hamil',
            'email' => 'ibu.hamil2121@test.com',
            'password' => Hash::make('password123'),
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ])->assertStatus(200);

        $token = $loginResponse['authorization']['token'];

        $this->putJson('/api/profile', [
            'alamat' => 'Baru',
        ], [
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json'
        ])->assertStatus(404);

        DB::table('user_profile')->insert([
            'user_id'       => $user->user_id,
            'tanggal_lahir' => '1995-03-15',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $this->putJson('/api/profile', [
            'alamat' => 'Baru',
        ], [
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json'
        ])->assertOk()->assertJson(['status' => 'success']);
    }
}
