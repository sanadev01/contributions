<?php

return [
    'production' => [
        'createPackageUrl' => '',
        'deletePackageUrl' => '',
        'createManifestUrl' => '',
        'ratingPackageUrl' => '',
        'pickupRatingUrl' => '',
        'pickupShipmentUrl' => '',
        'pickupCancelUrl' => '',
        'trackingUrl' => '',
        'transactionSrc' => '',
        'userName' => '',
        'password' => '',
        'shipperNumber' => '',
        'AccessLicenseNumber' => '',
    ],
    'testing' => [
        'createPackageUrl' => 'https://wwwcie.ups.com/ship/v1/shipments',
        'deletePackageUrl' => '',
        'createManifestUrl' => '',
        'ratingPackageUrl' => 'https://onlinetools.ups.com/ship/v1/rating/Rate',
        'pickupRatingUrl' => 'https://wwwcie.ups.com/ship/1707/pickups/rating',
        'pickupShipmentUrl' => 'https://wwwcie.ups.com/ship/1707/pickups',
        'pickupCancelUrl' => 'https://wwwcie.ups.com/ship/v1/pickups/prn',
        'trackingUrl' => 'https://wwwcie.ups.com/track/v1/details/',
        'transactionSrc' => 'HERCO',
        'userName' => 'hffinc1',
        'password' => 'Hdbrasilc4!',
        'shipperNumber' => '022VX0',
        'AccessLicenseNumber' => '5DA71F61D4F245F6',
    ],
];
