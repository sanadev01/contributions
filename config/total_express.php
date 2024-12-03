<?php

return [
    'test' => [
        'email' => env('TOTAL_EXPRESS_TEST_EMAIL'),
        'password' => env('TOTAL_EXPRESS_TEST_PASSWORD'),
        'baseUrl' => env('TOTAL_EXPRESS_TEST_BASE_URL'),
        'contractId' => env('TOTAL_EXPRESS_TEST_CONTRACT_ID'),
        'container' => [
            'email' => env('TOTAL_EXPRESS_TEST_CONTAINER_EMAIL'),
            'password' => env('TOTAL_EXPRESS_TEST_CONTAINER_PASSWORD'),
            'baseURL' => env('TOTAL_EXPRESS_TEST_CONTAINER_BASE_URL'),
        ],
    ],
    'production' => [
        'email' => env('TOTAL_EXPRESS_PROD_EMAIL'),
        'password' => env('TOTAL_EXPRESS_PROD_PASSWORD'),
        'baseUrl' => env('TOTAL_EXPRESS_PROD_BASE_URL'),
        'contractId' => env('TOTAL_EXPRESS_PROD_CONTRACT_ID'),
        'container' => [
            'email' => env('TOTAL_EXPRESS_PROD_CONTAINER_EMAIL'),
            'password' => env('TOTAL_EXPRESS_PROD_CONTAINER_PASSWORD'),
            'baseURL' => env('TOTAL_EXPRESS_PROD_CONTAINER_BASE_URL'),
        ],
    ],
];
