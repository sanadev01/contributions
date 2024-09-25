<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;
use Illuminate\Http\Request;

class PRCUnitRegisterController extends Controller
{
    public function __invoke(Container $container)
    {
        if ($container->orders->isEmpty()) {
            session()->flash('alert-danger','Please add parcels to this container');
            return back();
        }
       
        $client = new Client();
        $response = $client->registerPRCUnit($container);

        if (!$response->success){
            session()->flash('alert-danger',$response->message);
            return back();
        }

        $container->update([
            'customs_response_list' => $response->result->id
        ]);

        session()->flash('alert-success','Custom PRC Registeration is Successful.');
        return back();
    }
}
