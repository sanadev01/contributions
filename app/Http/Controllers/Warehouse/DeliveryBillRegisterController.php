<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;
use App\Services\GePS\Client as GePSClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Warehouse\Container;

class DeliveryBillRegisterController extends Controller
{
    public function __invoke(DeliveryBill $deliveryBill)
    {
        if ($deliveryBill->containers->isEmpty()) {
            session()->flash('alert-danger','Please add containers to this delivery bill');
            return back();
        }

        if ($deliveryBill->containers->first()->services_subclass_code == Container::CONTAINER_COLOMBIA_NX) {
            
            $response = random_int(100000, 999999).'-'.random_int(1000, 9999).'-'.random_int(100000, 999999);
            $cnd38Code = $deliveryBill->id.random_int(1000, 9999);

            $deliveryBill->update([
                'cnd38_code' => $cnd38Code,
                'request_id' => $response
            ]);

        }elseif($deliveryBill->containers[0]->services_subclass_code == '537')  {
            $client = new GePSClient();
            $response = $client->registerDeliveryBillGePS($deliveryBill);

            if ($response['success'] == false) {
                session()->flash('alert-danger', $response['message']);
                return back();
            }
            $result = $response['data']->manifestresponse;
            $cn38 = $result->manifestnbr;
            $manifest_pdf = $result->manifestpdf;
            $request_id = $response['data']->perfmilli;
            $deliveryBill->update([
                'request_id' => $request_id,
                'cnd38_code' => $cn38
            ]);
            Storage::put("labels/{$cn38}.pdf", base64_decode($manifest_pdf));

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
