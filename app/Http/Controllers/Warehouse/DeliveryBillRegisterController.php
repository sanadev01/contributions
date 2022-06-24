<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;
use App\Services\PostNL\Client as NLClient;
use Illuminate\Http\Request;

class DeliveryBillRegisterController extends Controller
{
    public function __invoke(DeliveryBill $deliveryBill)
    {
        if ($deliveryBill->containers->isEmpty()) {
            session()->flash('alert-danger','Please add containers to this delivery bill');
            return back();
        }

        if($deliveryBill->containers[0]->services_subclass_code == 'PostNL') {
            $client = new NLClient();
            $response = $client->registerDeliveryBillPostNL($deliveryBill);

            if ($response['success'] == false) {
                session()->flash('alert-danger', $response['message']);
                return back();
            }

            $result = $response['data']->data->details;
            $cn38 = $result[0]->manifest_codes[0];
            $url = $result[0]->url;
            $deliveryBill->update([
                'request_id' => $url,
                'cnd38_code' => $cn38
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
