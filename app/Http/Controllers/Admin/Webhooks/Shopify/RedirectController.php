<?php

namespace App\Http\Controllers\Admin\Webhooks\Shopify;

use App\Http\Controllers\Controller;
use App\Models\Connect;
use App\Services\StoreIntegrations\Shopify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    public function __invoke(Request $request, Shopify $shopifyClient)
    {
        if ( !Auth::check() ){
            session()->put('shopify.redirect', $request->fullUrl());
            return redirect()->route('login');
        }

        
        try {
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
        } catch (\Exception $ex) {
            //throw $th;
        }

        session()->flash('alert-success','Store Added Successfully');
        return \redirect()->route('admin.connect.guide');
    }
}
