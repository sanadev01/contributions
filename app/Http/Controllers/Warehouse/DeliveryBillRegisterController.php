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
        if($deliveryBill->containers[0]->services_subclass_code == ShippingService::GePS)  {            
            // $client = new GePSClient();
            // $response = $client->registerDeliveryBillGePS($deliveryBill);
            
            // if ($response['success'] == false) {
                //     session()->flash('alert-danger', $response['message']);
                //     return back();
                // }
                // $result = $response['data']->manifestresponse;
                // $cn38 = $result->manifestnbr;
                // $manifest_pdf = $result->manifestpdf;
                // $request_id = $response['data']->perfmilli;
            $date = date('ymd', strtotime(Carbon::now()));
            $cn38 = $deliveryBill->containers[0]->dispatch_number.''.$code = $deliveryBill->containers[0]->seal_no.''.$date;
            $request_id = str_random(32);
            $deliveryBill->update([
                'request_id' => $request_id,
                'cnd38_code' => $cn38
            ]);
            // Storage::put("labels/{$cn38}.pdf", base64_decode($manifest_pdf));
        } elseif($deliveryBill->isDirectLink())  {
            $date = date('ymd i', strtotime(Carbon::now()));
            $deliveryBill->update([
                'cnd38_code' => $date,
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
