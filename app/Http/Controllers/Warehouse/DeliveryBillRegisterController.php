<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;

class DeliveryBillRegisterController extends Controller
{
    public function __invoke(DeliveryBill $deliveryBill)
    {
        if ($deliveryBill->containers->isEmpty()) {
            session()->flash('alert-danger','Please add containers to this delivery bill');
            return back();
        }
     

        // if ($deliveryBill->isRegistered()) {
        //     session()->flash('alert-danger','This delivery bill has already been registered');
        //     return back();
        // }
       
        if ($deliveryBill->containerShippingService(ShippingService::TOTAL_EXPRESS)) {
             $deliveryBill->update([
                'cnd38_code' => $deliveryBill->setCN38Code(),
                'request_id' => $deliveryBill->setRandomRequestId()
            ]);
        } 

 
        if($deliveryBill->isAnjunChina() ||$deliveryBill->isGePS() || $deliveryBill->isSwedenPost() || $deliveryBill->isPostPlus() || $deliveryBill->isGSS() || $deliveryBill->isGDE() || $deliveryBill->isHDExpress()|| $deliveryBill->isHoundExpress()){
            $deliveryBill->update([
                'cnd38_code' => $deliveryBill->id.''.$deliveryBill->setCN38Code(),
                'request_id' => $deliveryBill->setRandomRequestId()
            ]);
            
        } else {

            $client = new Client();
            $response = $client->registerDeliveryBill($deliveryBill);

            if ( $response instanceof PackageError){
                session()->flash('alert-danger',$response->getErrors());
                return back();
            }

            $deliveryBill->update([
                'request_id' => $response
            ]);
        }

        session()->flash('alert-success','Delivery Bill Request Created. Please Check 30 minutes later to download bill');
        return back();
    }
}
