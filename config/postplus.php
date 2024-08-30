<?php

return [
    'test' => [
        'x-api-key' => env('POSTPLUS_TEST_X_API_KEY'),
        'base_uri' => env('POSTPLUS_TEST_BASE_URI', 'https://default.test.uri'),
    ],
    'production' => [
        'x-api-key' => env('POSTPLUS_PROD_X_API_KEY'),
        'base_uri' => env('POSTPLUS_PROD_BASE_URI', 'https://default.production.uri'),
    ],
];
