<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:5173')],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
//'exposed_headers' => [],

// Headers listed here can be read by JavaScript in the browser (e.g., via response.headers.get()).
// Leave empty unless your API returns custom headers you need in the frontend.

//'max_age' => 0,

// How long (in seconds) the browser should cache CORS preflight (OPTIONS) responses.
// 0 = no caching (fine for development). In production, consider 600 (10 mins) for better performance.
