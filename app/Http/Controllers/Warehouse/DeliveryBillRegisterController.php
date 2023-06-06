<?php

namespace App\Http\Controllers\Warehouse;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use Illuminate\Support\Facades\Storage;
use App\Services\GePS\Client as GePSClient;
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

        if ($deliveryBill->isRegistered()) {
            session()->flash('alert-danger','This delivery bill has already been registered');
            return back();
        }
        if($deliveryBill->isGePS() || $deliveryBill->isSwedenPost() || $deliveryBill->isPostPlus() || $deliveryBill->isGDE())  {            
            
            $deliveryBill->update([
                'cnd38_code' => $deliveryBill->setCN38Code(),
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
