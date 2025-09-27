<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'], // Bebaskan semua endpoint
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // <-- Sementara izinkan semua origin, nanti bisa dibatasi
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,

];
