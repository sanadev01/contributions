<?php

return [
    'test' => [
        'partner_key' => env('HOUND_TEST_PARTNER_KEY'),
        'base_url' => env('HOUND_TEST_BASE_URL', 'https://default.test.url'),
    ],
    'production' => [
        'partner_key' => env('HOUND_PROD_PARTNER_KEY'),
        'base_url' => env('HOUND_PROD_BASE_URL', 'https://default.production.url'),
    ],
];
