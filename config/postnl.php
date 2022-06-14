<?php

return [
    'production' => [
        'createLabelUrl' => 'https://clients.postnl.post/v7/api/contentlabel/generate',
        'deleteLabelUrl' => 'https://clients.postnl.post/v7/api/label/cancel',
    ],
    'testing' => [
        'createLabelUrl' => 'https://clients.postnl.a02.cldsvc.net/v7/api/contentlabel/generate',
        'deleteLabelUrl' => 'https://clients.postnl.a02.cldsvc.net/v7/api/label/cancel',
    ],

];
