<?php

return [
    'production' => [
        'createLabelUrl' => env('USPS_PROD_CREATE_LABEL_URL'),
        'deleteLabelUrl' => env('USPS_PROD_DELETE_LABEL_URL'),
        'createManifestUrl' => env('USPS_PROD_CREATE_MANIFEST_URL'),
        'getPriceUrl' => env('USPS_PROD_GET_PRICE_URL'),
        'trackingUrl' => env('USPS_PROD_TRACKING_URL'),
        'addressValidationUrl' => env('USPS_PROD_ADDRESS_VALIDATION_URL'),
        'email' => env('USPS_PROD_EMAIL'),
        'password' => env('USPS_PROD_PASSWORD'),
    ],
    'testing' => [
        'createLabelUrl' => env('USPS_TEST_CREATE_LABEL_URL'),
        'deleteLabelUrl' => env('USPS_TEST_DELETE_LABEL_URL'),
        'createManifestUrl' => env('USPS_TEST_CREATE_MANIFEST_URL'),
        'getPriceUrl' => env('USPS_TEST_GET_PRICE_URL'),
        'trackingUrl' => env('USPS_TEST_TRACKING_URL'),
        'addressValidationUrl' => env('USPS_TEST_ADDRESS_VALIDATION_URL'),
        'email' => env('USPS_TEST_EMAIL'),
        'password' => env('USPS_TEST_PASSWORD'),
    ],
];
