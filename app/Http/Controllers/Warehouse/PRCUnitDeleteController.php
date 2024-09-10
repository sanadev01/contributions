<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;
use Illuminate\Http\Request;

class PRCUnitDeleteController extends Controller
{
    public function __invoke(Container $container)
    {
        if (!$container->isPRCRegistered()) {
            session()->flash('alert-danger','This container is not PRC registered. Please register first.');
            return back();
        }
       
        $client = new Client();
        $response = $client->deletePRCUnit($container);
        if (!$response->success){
            session()->flash('alert-danger',$response->message);
            return back();
        }

        $container->update([
            'customs_response_list' => null
        ]);

        session()->flash('alert-success','Custom PRC Batch Deleted Successfully');
        return back();
    }
}
