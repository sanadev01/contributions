<?php

namespace App\Http\Controllers\Warehouse;
use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\TotalExpress\Services\TotalExpressMasterBox;
class HoundUnitRegisterController extends Controller
{
    public function createMasterBox(Request $request)
    {
        $container = Container::find($request->id);
        if ($container->orders->isEmpty()) {
            session()->flash('alert-danger','Please add parcels to this container');
            return back();
        }

        // $client = new TotalExpressMasterBox($container);
        // $request =  $client->requestMasterBox($container);
        if ($container->unit_code == null) {
            $code = optional(optional($container->orders->first()->recipient)->country)->code;

            $container->update([
                'unit_code' =>   'HD'.date('d').date('m').sprintf("%020d", $container->id).$code,
                'unit_response_list' => json_encode(['cn35'=> 'HD'.date('d').date('m').sprintf("%020d", $container->id)]).$code,
                'response' => '1',
            ]);
            session()->flash('alert-success','Package Registration success. You can print Label now');
            return back();
        }
        else{
            session()->flash('alert-danger','Package Already Registered.');
            return back();
        }
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
