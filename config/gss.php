<?php

return [
    'test' => [
        'userId' => env('GSS_TEST_USER_ID'),
        'password' => env('GSS_TEST_PASSWORD'),
        'locationId' => env('GSS_TEST_LOCATION_ID'),
        'workStationId' => env('GSS_TEST_WORKSTATION_ID'),
        'baseUrl' => env('GSS_TEST_BASE_URL', 'https://default.test.url'),
    ],
    'production' => [
        'userId' => env('GSS_PROD_USER_ID'),
        'password' => env('GSS_PROD_PASSWORD'),
        'locationId' => env('GSS_PROD_LOCATION_ID'),
        'workStationId' => env('GSS_PROD_WORKSTATION_ID'),
        'baseUrl' => env('GSS_PROD_BASE_URL', 'https://default.production.url'),
    ],
];
