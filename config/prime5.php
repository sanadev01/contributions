<?php

return [
    'test' => [
        'secret' => env('PRIME5_TEST_SECRET'),
        'token' => env('PRIME5_TEST_TOKEN'),
        'host' => env('PRIME5_TEST_HOST', 'http://default.test.host'),
        'baseUrl' => env('PRIME5_TEST_BASE_URL', 'http://default.test.base.url'),
        'container' => [
            'userName' => env('PRIME5_TEST_CONTAINER_USER_NAME'),
            'password' => env('PRIME5_TEST_CONTAINER_PASSWORD'),
            'baseURL' => env('PRIME5_TEST_CONTAINER_BASE_URL', 'https://default.test.container.url'),
        ],
    ],
    'production' => [
        'secret' => env('PRIME5_PROD_SECRET'),
        'token' => env('PRIME5_PROD_TOKEN'),
        'host' => env('PRIME5_PROD_HOST', 'http://default.prod.host'),
        'baseUrl' => env('PRIME5_PROD_BASE_URL', 'http://default.prod.base.url'),
        'container' => [
            'userName' => env('PRIME5_PROD_CONTAINER_USER_NAME'),
            'password' => env('PRIME5_PROD_CONTAINER_PASSWORD'),
            'baseURL' => env('PRIME5_PROD_CONTAINER_BASE_URL', 'https://default.prod.container.url'),
        ],
    ],
    'trackUrl' => env('PRIME5_TRACK_URL', 'https://default.track.url'),
    'trackApiKey' => env('PRIME5_TRACK_API_KEY'),
];
