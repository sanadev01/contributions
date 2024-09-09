<?php

namespace App\Http\Controllers\Warehouse;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\TotalExpress\Services\TotalExpressMasterBox;

class UnitRegisterFactoryController extends Controller
{
    public function createMasterBox(Request $request)
    {
        $container = Container::find($request->id);
        if ($container->orders->isEmpty()) {
            session()->flash('alert-danger','Please add parcels to this container');
            return back();
        }

        if(!$container->unit_code){
            $container->update([
                'unit_code' => 'HDC'.date('d').date('m').sprintf("%07d", $container->id).'CO',
                'response' => true,
            ]); 
        }

        session()->flash('alert-success','registered successfully!');
        return back();
              
         
    }

    public function consultMasterBox(Request $request)
    {
        $container = Container::find($request->id);
        if($container->unit_response_list) {
            $apiRequest = (new TotalExpressMasterBox($container))->consultCreateMasterBox($container);

            $response = $apiRequest->getData();
            if ($response->isSuccess){
                session()->flash('alert-success', $response->message);
                return back();
                
            } else {
                session()->flash('alert-danger',$response->message);
                return back();
            } 
        } else {
            session()->flash('alert-danger', 'Request ID Not Found');
            return back();
        } 
 
    }
}
