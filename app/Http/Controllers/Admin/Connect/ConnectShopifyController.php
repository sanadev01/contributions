<?php

namespace App\Http\Controllers\Admin\Connect;

use App\Http\Controllers\Controller;
use App\Http\Requests\Connect\Shopify\CreateRequest;
use App\Models\Connect;
use App\Services\StoreIntegrations\Shopify;
use Illuminate\Http\Request;

class ConnectShopifyController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Connect::class);
    }

    public function create()
    {
        return view('admin.connects.shopify.create');
    }

    public function store(CreateRequest $request, Shopify $shopifyClient)
    {
        if ( Connect::where('store_url','LIKE',"%{$request->connect_store_url}%")->first() ){
            session()->flash('alert-warning','Store is Already Added');
            return back();
        }
        
        $redirectUri = $shopifyClient->getRedirectUrl($request->connect_store_url,$request->all());
        return redirect()->away($redirectUri);
    }
}
