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
    
            if ( !$accessToken['store_url'] ){
                $connect= Connect::create(
                    $accessToken
                );
            }else{
                $connect = Connect::where('store_url','LIKE',"{$accessToken['store_url']}%")->first();
                $connect->update($accessToken);
            }
            $wewbhook = $shopifyClient->addWebook($connect);

            $connect->update([
                'extra_data' => [
                    'webhook' => $wewbhook->webhook
                ]
            ]);
            session()->flash('alert-success','Store Added Successfully');
        } catch (\Exception $ex) {
            if ( str_contains($ex->getMessage(),422) ){
                session()->flash('alert-success','Store Updated Successfully');
            }else{
                session()->flash('alert-danger',$ex->getMessage());
            }
        }

        return \redirect()->route('admin.connect.guide');
    }
}
