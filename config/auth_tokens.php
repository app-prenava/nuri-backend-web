<?php

return [
    'ttl_seconds' => [
        'bidan'   => env('AUTH_TTL_BIDAN_SECONDS', 12 * 60 * 60), 
        'dinkes'  => env('AUTH_TTL_DINKES_SECONDS', 12 * 60 * 60),
        'default' => env('AUTH_TTL_DEFAULT_SECONDS', 0),          
    ],
];
