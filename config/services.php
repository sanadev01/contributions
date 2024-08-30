    <?php

    return [

        /*
        |--------------------------------------------------------------------------
        | Third Party Services
        |--------------------------------------------------------------------------
        |
        | This file is for storing the credentials for third party services such
        | as Mailgun, Postmark, AWS and more. This file provides the de facto
        | location for this type of information, allowing packages to have
        | a conventional file to locate the various service credentials.
        |
        */

        'mailgun' => [
            'domain' => env('MAILGUN_DOMAIN'),
            'secret' => env('MAILGUN_SECRET'),
            'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        ],

        'postmark' => [
            'token' => env('POSTMARK_TOKEN'),
        ],
        'ses' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        ],
        'stripe' => [
            'secret' => env('STRIPE_SECRET'),
        ],
        'sp-api-prod' => [
            "SP_APP_ID" => env('SP_API_PROD_APP_ID'),
            "SP_APP_CLIENT_ID" => env('SP_API_PROD_APP_CLIENT_ID'),
            "SP_APP_CLIENT_SECRET" => env('SP_API_PROD_APP_CLIENT_SECRET'),
            "SP_APP_REDIRECT" => env('SP_API_PROD_APP_REDIRECT'), 
            "HD_REDIRECT" => "https://app.homedeliverybr.com/amazon/home",
        ],
        'sp-api-dev' => [ 
            "SP_APP_ID" => env('SP_API_DEV_APP_ID'),
            "SP_APP_CLIENT_ID" => env('SP_API_DEV_APP_CLIENT_ID'),
            "SP_APP_CLIENT_SECRET" => env('SP_API_DEV_APP_CLIENT_SECRET'),
            "SP_APP_REDIRECT" => env('SP_API_DEV_APP_REDIRECT'),
            "HD_REDIRECT" => "https://dev.homedeliverybr.com/amazon/home",
        ]
    ];
