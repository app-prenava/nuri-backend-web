<?php

namespace Tests\Feature;

use Tests\TestCase;

class EnvSanityTest extends TestCase
{
    public function test_env_is_testing_and_uses_sqlite(): void
    {
        $this->assertEquals('testing', app()->environment());
        $this->assertEquals('sqlite', config('database.default'));
    }
}
