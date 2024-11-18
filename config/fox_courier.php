<?php
return [
    'test' => [
        'api_key'     => env('FOX_TEST_API_KEY'),
        'api_secret'  => env('FOX_TEST_API_SECRET'),
        'token'       => env('FOX_TEST_TOKEN'),
        'base_uri'    => env('FOX_TEST_BASE_URI'),
    ],
    'production' => [
        'api_key'     => env('FOX_PROD_API_KEY'),
        'api_secret'  => env('FOX_PROD_API_SECRET'),
        'token'       => env('FOX_PROD_TOKEN'),
        'base_uri'    => env('FOX_PROD_BASE_URI'),
    ],
];
