<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // Apply CORS to API routes and Sanctum's CSRF route
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Allow all HTTP methods
    'allowed_methods' => ['*'],

    // Allowed origins for the frontend (Vite dev server, etc.)
    // You can override via CORS_ALLOWED_ORIGINS in .env as a comma-separated list
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:5173,http://127.0.0.1:5173')),

    'allowed_origins_patterns' => [],

    // Allow all headers
    'allowed_headers' => ['*'],

    // No special exposed headers
    'exposed_headers' => [],

    // No caching of preflight results by default
    'max_age' => 0,

    // Enable credentials (needed for cookies / Sanctum)
    'supports_credentials' => true,
];


