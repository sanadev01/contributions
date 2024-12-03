<?php

return [
    'production' => [
        'createPackageUrl' => env('UPS_PROD_CREATE_PACKAGE_URL'),
        'deletePackageUrl' => env('UPS_PROD_DELETE_PACKAGE_URL'),
        'createManifestUrl' => env('UPS_PROD_CREATE_MANIFEST_URL'),
        'ratingPackageUrl' => env('UPS_PROD_RATING_PACKAGE_URL'),
        'pickupRatingUrl' => env('UPS_PROD_PICKUP_RATING_URL'),
        'pickupShipmentUrl' => env('UPS_PROD_PICKUP_SHIPMENT_URL'),
        'pickupCancelUrl' => env('UPS_PROD_PICKUP_CANCEL_URL'),
        'trackingUrl' => env('UPS_PROD_TRACKING_URL'),
        'transactionSrc' => env('UPS_PROD_TRANSACTION_SRC'),
        'userName' => env('UPS_PROD_USERNAME'),
        'password' => env('UPS_PROD_PASSWORD'),
        'shipperNumber' => env('UPS_PROD_SHIPPER_NUMBER'),
        'AccessLicenseNumber' => env('UPS_PROD_ACCESS_LICENSE_NUMBER'),
    ],
    'testing' => [
        'createPackageUrl' => env('UPS_TEST_CREATE_PACKAGE_URL', 'https://wwwcie.ups.com/ship/v1/shipments'),
        'deletePackageUrl' => env('UPS_TEST_DELETE_PACKAGE_URL'),
        'createManifestUrl' => env('UPS_TEST_CREATE_MANIFEST_URL'),
        'ratingPackageUrl' => env('UPS_TEST_RATING_PACKAGE_URL', 'https://onlinetools.ups.com/ship/v1/rating/Rate'),
        'pickupRatingUrl' => env('UPS_TEST_PICKUP_RATING_URL', 'https://wwwcie.ups.com/ship/1707/pickups/rating'),
        'pickupShipmentUrl' => env('UPS_TEST_PICKUP_SHIPMENT_URL', 'https://wwwcie.ups.com/ship/1707/pickups'),
        'pickupCancelUrl' => env('UPS_TEST_PICKUP_CANCEL_URL', 'https://wwwcie.ups.com/ship/v1/pickups/prn'),
        'trackingUrl' => env('UPS_TEST_TRACKING_URL', 'https://wwwcie.ups.com/track/v1/details/'),
        'transactionSrc' => env('UPS_TEST_TRANSACTION_SRC'),
        'userName' => env('UPS_TEST_USERNAME'),
        'password' => env('UPS_TEST_PASSWORD'),
        'shipperNumber' => env('UPS_TEST_SHIPPER_NUMBER'),
        'AccessLicenseNumber' => env('UPS_TEST_ACCESS_LICENSE_NUMBER'),
    ],
];
