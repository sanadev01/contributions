<?php

namespace App\Http\Controllers\Admin\Connect;

use App\Http\Controllers\Controller;
use App\Models\Connect;
use App\Repositories\ConnectReporistory;
use App\Services\StoreIntegrations\Shopify;
use Illuminate\Http\Request;

class ConnectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ConnectReporistory $connectReporistory)
    {
        $connects = $connectReporistory->get();
        return view('admin.connects.index',compact('connects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.connects.create');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Connect  $connect
     * @return \Illuminate\Http\Response
     */
    public function destroy(Connect $connect, Shopify $shopifyClient)
    {
        if ( !$shopifyClient->deleteWebhook($connect) ){
            session()->flash('alert-danger','Error deleting webhook');
        }

        $connect->delete();
        session()->flash('alert-success','Intergration Deleted');
        return back();
    }
}
