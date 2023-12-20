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
            "SP_APP_ID" => 'amzn1.sp.solution.8516038e-4f0c-466f-8103-95fa2d5ec52c',
            "SP_APP_CLIENT_ID" => 'amzn1.application-oa2-client.fa76075fa12245679a286245dd0456e6',
            "SP_APP_CLIENT_SECRET" => 'amzn1.oa2-cs.v1.31af2e97b8ccc5c0a01e494935186a4ae5b315eac19720da680ddf2fd99fc300',
            "SP_APP_REDIRECT" => 'https://sp-dev.homedeliverybr.com/sp/register',
        ],
        'sp-api-dev' => [
            "SP_APP_ID" => 'amzn1.sp.solution.8516038e-4f0c-466f-8103-95fa2d5ec52c',
            "SP_APP_CLIENT_ID" => 'amzn1.application-oa2-client.fa76075fa12245679a286245dd0456e6',
            "SP_APP_CLIENT_SECRET" => 'amzn1.oa2-cs.v1.31af2e97b8ccc5c0a01e494935186a4ae5b315eac19720da680ddf2fd99fc300',
            "SP_APP_REDIRECT" => 'https://sp-dev.homedeliverybr.com/sp/register',
        ]

    ];
