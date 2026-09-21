<?php

require_once __DIR__ . '/env.php';

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'bsVersion' => '5.x',
    // POST /api/works: optional bearer token (empty = public endpoint) and allowed CORS origins.
    'apiToken' => env('API_TOKEN', ''),
    'apiCorsOrigins' => array_values(array_filter(array_map('trim', explode(',', env('API_CORS_ORIGINS', '*'))))),
];
