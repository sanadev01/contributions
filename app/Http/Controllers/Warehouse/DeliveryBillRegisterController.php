<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;
use App\Services\GePS\Client as GePSClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class DeliveryBillRegisterController extends Controller
{
    public function __invoke(DeliveryBill $deliveryBill)
    {
        if ($deliveryBill->containers->isEmpty()) {
            session()->flash('alert-danger','Please add containers to this delivery bill');
            return back();
        }

        if($deliveryBill->containers[0]->services_subclass_code == 'KP') {
            $client = new GePSClient();
            $response = $client->registerDeliveryBillGePS($deliveryBill);

            if ($response['success'] == false) {
                session()->flash('alert-danger', $response['message']);
                return back();
            }

            $result = $response->manifestresponse;
            $cn38 = $result->manifestnbr;
            $manifest_pdf = $result->manifestpdf;
            $deliveryBill->update([
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
