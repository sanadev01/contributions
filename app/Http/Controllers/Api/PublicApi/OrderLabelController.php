<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use App\Repositories\LabelRepository;
use Illuminate\Support\Facades\Storage;
use App\Repositories\USPSLabelRepository;
use App\Repositories\CorrieosChileLabelRepository;
use App\Repositories\CorrieosBrazilLabelRepository;

class OrderLabelController extends Controller
{
    public function __invoke(Request $request, Order $order, CorrieosBrazilLabelRepository $labelRepository, CorrieosChileLabelRepository $chile_labelRepository, USPSLabelRepository $usps_labelRepository)
    {
        
        $this->authorize('canPrintLableViaApi',$order);
        
        if ( !$order->isPaid() &&  getBalance() < $order->gross_total){
            return apiResponse(false,"Not Enough Balance. Please Recharge your account.");
        }

        $labelData = null;

        // For Correos Chile
        if($order->recipient->country_id == Order::CHILE)
        {
            $chile_labelRepository->handle($order);

            $error = $chile_labelRepository->getChileErrors();

            if(!$error)
            {
                if ( !$order->isPaid() &&  getBalance() >= $order->gross_total ){
                    $order->update([
                        'is_paid' => true,
                        'status' => Order::STATUS_PAYMENT_DONE
                    ]);
                    chargeAmount($order->gross_total,$order);
                }
                
                return apiResponse(true,"Lable Generated successfully.",[
                    'url' => route('order.label.download',$order),
                    'tracking_code' => $order->corrios_tracking_code
                ]);
            }

            return apiResponse(false, $error);
            
        }

        // For USPS
        if($order->recipient->country_id == Order::USPS)
        {
            $usps_labelRepository->handle($order);

            $error = $usps_labelRepository->getUSPSErrors();

            if(!$error)
            {
                if ( !$order->isPaid() &&  getBalance() >= $order->gross_total ){
                    $order->update([
                        'is_paid' => true,
                        'status' => Order::STATUS_PAYMENT_DONE
                    ]);
                    chargeAmount($order->gross_total,$order);
                }
                
                return apiResponse(true,"Lable Generated successfully.",[
                    'url' => route('order.label.download',$order),
                    'tracking_code' => $order->corrios_tracking_code
                ]);
            }

            return apiResponse(false, $error);
            
        }
        if ( $request->update_label === 'true' ){
            $labelData = $labelRepository->update($order);
        }else{
            $labelData = $labelRepository->get($order);
        }

        $order->refresh();

        if ( $labelData ){
            Storage::put("labels/{$order->corrios_tracking_code}.pdf", $labelData);
        }

        if ( $labelRepository->getError() ){
            return apiResponse(false,$labelRepository->getError());
        }

        if ( !$order->isPaid() &&  getBalance() >= $order->gross_total ){
            $order->update([
                'is_paid' => true,
                'status' => Order::STATUS_PAYMENT_DONE
            ]);
            chargeAmount($order->gross_total,$order);
        }

        return apiResponse(true,"Lable Generated successfully.",[
            'url' => route('order.label.download',$order),
            'tracking_code' => $order->corrios_tracking_code
        ]);

    }
}
