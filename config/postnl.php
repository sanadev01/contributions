<?php

return [
    'production' => [
        'createLabelUrl' => 'https://clients.postnl.post/v7/api/contentlabel/generate',
        'canceLabelUrl' => 'https://clients.postnl.post/v7/api/label/cancel',
        'createAssistLabelUrl' => 'https://clients.postnl.post/v7/api/assistlabel/generate',
        'createManifest' => 'https://clients.postnl.post/v7/api/label/closeout',
        'addAirwayBill' => 'https://clients.postnl.post/v7/api/manifest/addmawb',
        'getTrackingUrl' => 'https://clients.postnl.post/v7/api/tracking/item',
    ],
    'testing' => [
        'createLabelUrl' => 'https://clients.postnl.a02.cldsvc.net/v7/api/contentlabel/generate',
        'canceLabelUrl' => 'https://clients.postnl.a02.cldsvc.net/v7/api/label/cancel',
        'createAssistLabelUrl' => 'https://clients.postnl.a02.cldsvc.net/v7/api/assistlabel/generate',
        'createManifest' => 'https://clients.postnl.a02.cldsvc.net/v7/api/label/closeout',
        'addAirwayBill' => 'https://clients.postnl.a02.cldsvc.net/v7/api/manifest/addmawb',
        'getTrackingUrl' => 'https://clients.postnl.a02.cldsvc.net/v7/api/tracking/item',
    ],

];
