<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;
use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Repositories\Warehouse\DeliveryBillRepository;

class DeliveryBillRegisterController extends Controller
{
    public function __invoke(DeliveryBill $deliveryBill, DeliveryBillRepository $deliveryBillRepository)
    {
        if ($deliveryBill->containers->isEmpty()) {
            session()->flash('alert-danger','Please add containers to this delivery bill');
            return back();
        }

        if ($deliveryBill->isRegistered()) {
            session()->flash('alert-danger','this delivery bill has already been registered');
            return back();
        }

        $firstContainer = $deliveryBill->containers()->first();

        if ($firstContainer->services_subclass_code == Container::CONTAINER_MILE_EXPRESS) {
            
            $deliveryBillRepository->processMileExpressBill($deliveryBill, $firstContainer);
            $error = $deliveryBillRepository->getError();

            if ($error) {
                session()->flash('alert-danger',$error);
                return back();
            }

        }else {
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
