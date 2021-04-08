<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;
use Illuminate\Http\Request;

class DeliveryBillStatusUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param DeliveryBill $deliveryBill
     * @return \Illuminate\Http\Response
     */
    public function __invoke(DeliveryBill $deliveryBill)
    {
        $client = new Client();
        $response = $client->getDeliveryBillStatus($deliveryBill);

        if ( $response instanceof PackageError){
            session()->flash('alert-danger',$response->getErrors());
            return back();
        }

        if ( $response == null ){
            session()->flash('alert-warning','Delivery Bill is Not generated Yet');
            return back();
        }

        $deliveryBill->update([
            'cnd38_code' => $response
        ]);

        session()->flash('alert-danger','CN38 Generated Successfully You can download Delivery Bill now');
        return back();
    }


}
