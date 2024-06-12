<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;
use Illuminate\Http\Request;

class UnitRegisterController extends Controller
{
    public function __invoke(Container $container)
    {
        if ($container->orders->isEmpty()) {
            session()->flash('alert-danger','Please add parcels to this container');
            return back();
        }
       
        $client = new Client();
        $response = $client->createContainer($container);

        if ( $response instanceof PackageError){
            session()->flash('alert-danger',$response->getErrors());
            return back();
        }

        $container->update([
            'unit_code' => $response
        ]);

        session()->flash('alert-success','Package Registration success. You can print Label now');
        return back();
    }
}
