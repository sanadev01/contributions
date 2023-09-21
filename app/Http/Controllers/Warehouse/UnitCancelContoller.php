<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;

class UnitCancelContoller extends Controller
{
    public function __invoke(Container $container)
    {
        $client = new Client();
        $response = $client->destroy($container);
        $container->update([
            'unit_code' => null
        ]);
        if ( $response instanceof PackageError){
            session()->flash('alert-danger',$response->getErrors());
            return back();
        }
        session()->flash('alert-success','Package Registration Cancelled.');
        return back();
    }
}
