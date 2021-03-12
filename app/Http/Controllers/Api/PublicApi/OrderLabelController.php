<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\LabelRepository;
use Illuminate\Support\Facades\Storage;

class OrderLabelController extends Controller
{
    public function __invoke(Request $request, Order $order, LabelRepository $labelRepository)
    {
        $this->authorize('canPrintLableViaApi',$order);
        
        if ( !$order->isPaid() &&  getBalance() < $order->gross_total){
            return apiResponse(false,"Not Enough Balance. Please Recharge your account.");
        }

        if ( !$order->isPaid() &&  getBalance() >= $order->gross_total ){
            $order->update([
                'is_paid' => true,
                'status' => Order::STATUS_PAYMENT_DONE
            ]);

            chargeAmount($order->gross_total);
        }


        $labelData = null;

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

        return apiResponse(true,"Lable Generated successfully.",[
            'url' => route('order.label.download',$order),
            'tracking_code' => $order->corrios_tracking_code
        ]);

    }
}
