<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\Correios\Services\Brazil\Client;

class UnitCancelContoller extends Controller
{
    public function __invoke(Container $container)
    {
        $client = new Client();
        $response = $client->destroy($container);
        
        if ( $response == 1){
            $container->update([
                'unit_code' => null
            ]);
            session()->flash('alert-success','Package Registration Cancelled.');
            return back();
        }
        session()->flash('alert-danger','Something went wrong.');
        return back();
    }
}
