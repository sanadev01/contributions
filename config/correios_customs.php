<?php
return [
    'production' => [
        'customsBaseUri'  => 'https://clearing.homolog.botpag.ws',
        'clientId'       => 'herco_freight@botpag.com.br',
        'clientSecret'   => '229b8d0a5c8af1b9aba075b34f04b44d602fbef2',
        'webhookUrl'   => 'https:/app.homedeliverybr.com/customs-response'
    ],
    'testing' => [
        'customsBaseUri'  => 'https://clearing.homolog.botpag.ws',
        'clientId'       => 'herco_freight@botpag.com.br',
        'clientSecret'   => '229b8d0a5c8af1b9aba075b34f04b44d602fbef2',
        'webhookUrl'   => 'https://dev.homedeliverybr.com/customs-response'
    ],
];
