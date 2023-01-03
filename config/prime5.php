<?php
return [
    'test' => [
        'secret' => 'I7MIjTQBMZNXToMTA2PkOw',
        'token' => 'testa0wXdbpML6JGQ7NRP3O',
        'host' => 'qa.etowertech.com',
        'baseUrl' => 'http://qa.etowertech.com/',
        'container'=>[
            'userName' => 'HFFBagscanAPI',
            'password' => 'f65004ab2af0dc0f0cc063ee2b45e804d003d9d9a0cad02ce8933f4e0217415e',
            'baseURL'  => 'https://api-server-test.directlink.pncloud.se/'
        ]
    ],
    'production' => [
        'secret' => 'Q-P1jmxEOJLvQ-sEGIvunQ',
        'token' => 'pclmZ6zGl7o7Le3TXAd52X',
        'host' => 'us.etowertech.com',
        'baseUrl' => 'http://us.etowertech.com/',
        'container'=>[
            'userName' => 'HFFBagscanAPI',
            'password' => '13182a58e385abf1b02395288284902403ae89ccbf6fe0197388b99c83d083e4',
            'baseURL'  => 'https://api.directlink.com/bagscan?op=validateCredentials'
        ],
    ]
];