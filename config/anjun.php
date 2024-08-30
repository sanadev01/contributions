<?php
return [
    'production' => [
        'baseUri'           => env('ANJUN_PROD_BASE_URI', 'https://takeno.bailidaming.com'),
        'username'          => env('ANJUN_PROD_USERNAME'),
        'password'          => env('ANJUN_PROD_PASSWORD'),
        'bigPackageBaseURL' => env('ANJUN_PROD_BIG_PACKAGE_BASE_URL', 'http://api.bailidaming.com'),
        'token'             => env('ANJUN_PROD_TOKEN')
    ],
    'testing' => [
        'baseUri'           => env('ANJUN_TEST_BASE_URI', 'https://takeno.bailidaming.com'),
        'username'          => env('ANJUN_TEST_USERNAME'),
        'password'          => env('ANJUN_TEST_PASSWORD'),
        'bigPackageBaseURL' => env('ANJUN_TEST_BIG_PACKAGE_BASE_URL', 'http://api.bailidaming.com'),
        'token'             => env('ANJUN_TEST_TOKEN')
    ],

];
