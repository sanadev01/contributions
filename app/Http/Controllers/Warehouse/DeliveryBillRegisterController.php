<?php

namespace App\Http\Controllers\Warehouse;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use Illuminate\Support\Facades\Storage;
use App\Services\GePS\Client as GePSClient;
use App\Services\Correios\Models\PackageError;
use App\Services\Correios\Services\Brazil\Client;
use App\Services\PostNL\Client as NLClient;
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
            session()->flash('alert-danger','This delivery bill has already been registered');
            return back();
        }
        if($deliveryBill->isGePS())  {            
            
            $deliveryBill->update([
                'cnd38_code' => $deliveryBill->setCN38Code(),
                'request_id' => $deliveryBill->setRandomRequestId()
            ]);
            
        } elseif($deliveryBill->isSwedenPost())  {

            $deliveryBill->update([
                'cnd38_code' => $deliveryBill->setCN38Code(),
                'request_id' => $deliveryBill->setRandomRequestId()
            ]);

        }
        elseif ($deliveryBill->hasMileExpressService()) {
            
            $deliveryBillRepository->processMileExpressBill($deliveryBill, $deliveryBill->container());
            $error = $deliveryBillRepository->getError();

            if ($error) {
                session()->flash('alert-danger',$error);
                return back();
            }

            $deliveryBill->update([
                'cnd38_code' => $deliveryBill->setRandomCN38Code(),
                'request_id' => $deliveryBill->setRandomRequestId()
            ]);

        }elseif($deliveryBill->hasColombiaService()) {
            
            $deliveryBill->update([
                'cnd38_code' => $deliveryBill->setRandomCN38Code(),
                'request_id' => $deliveryBill->setRandomRequestId()
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

        }elseif($deliveryBill->containers[0]->services_subclass_code == 'PostNL') {
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
