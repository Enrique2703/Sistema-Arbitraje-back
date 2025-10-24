<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'api'), // 👈 por defecto usará el guard api
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    'guards' => [
        // Guard web (por si lo usas en panel admin u otros)
        'web' => [
            'driver' => 'session',
            'provider' => 'credenciales',
        ],

        // 👇 Guard JWT para tu API
        'api' => [
            'driver' => 'jwt', // 👈 necesario para Tymon\JWTAuth
            'provider' => 'credenciales',
        ],
    ],

    'providers' => [
        // 👇 Cambiamos de "users" a "credenciales"
        'credenciales' => [
            'driver' => 'eloquent',
            'model' => App\Models\Credencial::class,
        ],
    ],

    'passwords' => [
        'credenciales' => [
            'provider' => 'credenciales',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
