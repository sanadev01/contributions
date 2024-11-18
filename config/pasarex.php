<?php
return [
    'test' => [
        'token'    => env('PASAR_EX_TEST_TOKEN'),
        'base_uri' => env('PASAR_EX_TEST_BASE_URI'),
    ],
    'production' => [
        'token'    => env('PASAR_EX_PROD_TOKEN'),
        'base_uri' => env('PASAR_EX_PROD_BASE_URI'),
    ],
];
