<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Order;
use App\Events\OrderPaid;
use Illuminate\Http\Request;
// use App\Repositories\LabelRepository;
use App\Services\GePS\Client;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Repositories\UPSLabelRepository;
use App\Repositories\GePSLabelRepository;
use App\Repositories\USPSLabelRepository;
use App\Repositories\FedExLabelRepository;
use App\Repositories\SwedenPostLabelRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CorrieosChileLabelRepository;
use App\Repositories\CorrieosBrazilLabelRepository;
use App\Repositories\ColombiaLabelRepository;

class OrderLabelController extends Controller
{
    public function __invoke(Request $request, Order $order, CorrieosBrazilLabelRepository $corrieosBrazilLabelRepository, CorrieosChileLabelRepository $corrieosChileLabelRepository, 
                            USPSLabelRepository $uspsLabelRepository, UPSLabelRepository $upsLabelRepository, FedExLabelRepository $fedexLabelRepository, 
                            ColombiaLabelRepository $colombiaLabelRepository, GePSLabelRepository $gepsLabelRepository, SwedenPostLabelRepository $swedenpostLabelRepository)
    {
        $orders = new Collection;
        $this->authorize('canPrintLableViaApi',$order);
        
        if ( !$order->isPaid() &&  getBalance() < $order->gross_total){
            return apiResponse(false,"Not Enough Balance. Please Recharge your account.");
        }

        $labelData = null;

        //For USPS International services
        if ($order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL || $order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL) {
            
            $uspsLabelRepository->handle($order);
            $error = $uspsLabelRepository->getUSPSErrors();

            if(!$error)
            {
                return $this->processOrderPayment($order);
            }

            return apiResponse(false, $error);
        }

        // For Correos Chile
        if(($order->shippingService->service_sub_class == ShippingService::SRP || $order->shippingService->service_sub_class == ShippingService::SRM) && $order->recipient->country_id == Order::CHILE)
        {
            $corrieosChileLabelRepository->handle($order);

            $error = $corrieosChileLabelRepository->getChileErrors();

            if(!$error)
            {
                return $this->processOrderPayment($order);
            }

            return apiResponse(false, $error);
        }

        if($order->recipient->country_id == Order::US)
        {
            // For USPS
            if ($order->shippingService->service_sub_class == ShippingService::USPS_PRIORITY || $order->shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS) 
            {
                $uspsLabelRepository->handle($order);

                $error = $uspsLabelRepository->getUSPSErrors();
            }

            // For UPS
            if ($order->shippingService->service_sub_class == ShippingService::UPS_GROUND) {

                $upsLabelRepository->handle($order);
                $error = $upsLabelRepository->getUPSErrors();
            }

            // For FedEx
            if ($order->shippingService->service_sub_class == ShippingService::FEDEX_GROUND) {

                $fedexLabelRepository->handle($order);
                $error = $fedexLabelRepository->getFedExErrors();
            }
           
            if(!$error)
            {
                return $this->processOrderPayment($order);
            }

            return apiResponse(false, $error);
        }

        if($order->recipient->country_id == Order::COLOMBIA && $order->shippingService->isColombiaService()){
            
            $colombiaLabelRepository->handle($order);
            $error = $colombiaLabelRepository->getError();

            if(!$error)
            {
                return $this->processOrderPayment($order);
            }

            return apiResponse(false, $error);
        }

        // For Correios,  Global eParcel Brazil and Sweden Post(Prime5)
        if ($order->recipient->country_id == Order::BRAZIL) {
           
            if($order->shippingService->isGePSService()){

                $gepsLabelRepository->get($order);
                $error = $gepsLabelRepository->getError();
                if($error){
                   return apiResponse(false, $error);
                }
            }if($order->shippingService->isSwedenPostService()){

                $swedenpostLabelRepository->get($order);
                $error = $swedenpostLabelRepository->getError();
                if($error){
                   return apiResponse(false, $error);
                }
            }
            if($order->shippingService->isAnjunService() ||  $order->shippingService->isCorreiosService()){

                if ( $request->update_label === 'true' ){
                    $labelData = $corrieosBrazilLabelRepository->update($order);
                }else{
                    $labelData = $corrieosBrazilLabelRepository->get($order);
                }

                $order->refresh();

                if ( $labelData ){
                    Storage::put("labels/{$order->corrios_tracking_code}.pdf", $labelData);
                }

                if ( $corrieosBrazilLabelRepository->getError() ){
                    return apiResponse(false,$corrieosBrazilLabelRepository->getError());
                }
            }
            return $this->processOrderPayment($order);    
        }

    }

    private function processOrderPayment($order)
    {
        if ( !$order->isPaid() &&  getBalance() >= $order->gross_total ){
            $order->update([
                'is_paid' => true,
                'status' => Order::STATUS_PAYMENT_DONE
            ]);
            chargeAmount($order->gross_total,$order);
        }
        
        return apiResponse(true,"Lable Generated successfully.",[
            'url' => route('order.label.download',  encrypt($order->id)),
            'tracking_code' => $order->corrios_tracking_code
        ]);
    }

    public function cancelGePSLabel(Order $order)
    {
        $gepsClient = new Client();   
        $response = $gepsClient->cancelShipment($order->corrios_tracking_code);
        if (!$response['success']) {
            return apiResponse(false, $response['message']);
        }
        if($response['success']) {
            $order->update([
                'corrios_tracking_code' => null,
                'cn23' => null,
                'api_response' => null
            ]);
            return apiResponse(true,"Label Cancellation is Successful.",[
                'cancelled_tracking_code' => $response['data']->cancelshipmentresponse->tracknbr
            ]);
        }
    } 
 
}
