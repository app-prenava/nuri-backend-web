<?php

namespace Tests\Helpers;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait JwtTestHelpers
{
    protected function makeUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'name'          => 'Test User',
            'email'         => fake()->unique()->safeEmail(),
            'password'      => bcrypt('password123'),
            'role'          => 'ibu_hamil',
            'is_active'     => true,
            'token_version' => 1,
        ], $overrides));
    }

    protected function issueToken(User $user): string
    {
        return JWTAuth::claims([
            'uid'   => $user->user_id,
            'role'  => $user->role,
            'name'  => $user->name,
            'email' => $user->email,
            'tv'    => (int) $user->token_version,
        ])->fromUser($user);
    }
}
