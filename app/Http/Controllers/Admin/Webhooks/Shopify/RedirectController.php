<?php

namespace App\Http\Controllers\Admin\Webhooks\Shopify;

use App\Http\Controllers\Controller;
use App\Models\Connect;
use App\Services\StoreIntegrations\Shopify;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __invoke(Request $request, Shopify $shopifyClient)
    {
        $accessToken = $shopifyClient->getAccessToken($request);

        $connect= Connect::create(
            $accessToken
        );

        $wewbhook = $shopifyClient->addWebook($connect);

        $connect->update([
            'extra_data' => [
                'webhook' => $wewbhook->webhook
            ]
        ]);

        session()->flash('alert-success','Store Added Successfully');
        return \redirect()->route('admin.connect.index');
    }
}
