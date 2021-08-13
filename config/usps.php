<?php

return [
    'url' => env('USPS_URL', 'https://api-sandbox.myibservices.com/v1/labels'),
    'delete_label_url' => env('USPS_DELETE_LABEL_URL', 'https://api-sandbox.myibservices.com/v1/labels/'),
    'create_manifest_url' => env('USPS_CREATE_MANIFEST_URL', 'https://api-sandbox.myibservices.com/v1/manifests.json'),
    'email' => env('USPS_Email', 'ghaziislam3@gmail.com'),
    'password' => env('USPS_Password', 'Ikonic@1234'),
];