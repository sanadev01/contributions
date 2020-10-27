<?php

namespace App\Http\Controllers\Admin\Connect;

use App\Http\Controllers\Controller;
use App\Http\Requests\Connect\Shopify\CreateRequest;
use App\Services\StoreIntegrations\Shopify;
use Illuminate\Http\Request;

class ConnectShopifyController extends Controller
{
    public function create()
    {
        return view('admin.connects.shopify.create');
    }

    public function store(CreateRequest $request, Shopify $shopifyClient)
    {
        $redirectUri = $shopifyClient->getRedirectUrl($request->connect_store_url,$request->all());
        return redirect()->away($redirectUri);
    }
}
