<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */



        'paths' => ['api/*', 'certificates/*', 'sanctum/csrf-cookie', '*'], // Barcha yo‘llarga ruxsat berish
        'allowed_methods' => ['*'], // Barcha HTTP metodlariga ruxsat
        'allowed_origins' => ['*'], // Barcha domenlarga ruxsat
        'allowed_origins_patterns' => [],
        'allowed_headers' => ['*'], // Barcha headerlarga ruxsat
        'exposed_headers' => ['Content-Disposition'], // Fayl yuklab olish uchun kerak
        'max_age' => 0,
        'supports_credentials' => true, // Credential'larni qo‘llab-quvvatlash


];

