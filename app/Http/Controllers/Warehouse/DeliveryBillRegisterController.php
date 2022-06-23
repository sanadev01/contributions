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
        //dd($deliveryBill->containers);
        if($deliveryBill->containers[0]->services_subclass_code == 'PostNL') {
            $client = new NLClient();
            $response = $client->registerDeliveryBillPostNL($deliveryBill);
            //dd($response);
            if ( $response instanceof PackageError){
                session()->flash('alert-danger',$response->getErrors());
                return back();
            }
            $url = '';
            $cn38 = '';
            $result = $response->data->details;
            //dd($result);
            foreach ($result as $res) {
                $cn38 = $res->manifest_codes[0];
                $url = $res->url;
            }
            $deliveryBill->update([
                'request_id' => $url,
                'cnd38_code' => $cn38
            ]);

            session()->flash('alert-success','Delivery Bill Request Created. Please Check 30 minutes later to download bill');
            return back();

        } else {
            $client = new Client();
            $response = $client->registerDeliveryBill($deliveryBill);
            //dd($response);
            if ( $response instanceof PackageError){
                session()->flash('alert-danger',$response->getErrors());
                return back();
            }

            $deliveryBill->update([
                'request_id' => $response
            ]);

            session()->flash('alert-success','Delivery Bill Request Created. Please Check 30 minutes later to download bill');
            return back();
        }

    }
}
