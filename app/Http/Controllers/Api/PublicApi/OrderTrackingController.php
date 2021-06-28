<?php

namespace App\Http\Controllers\Api\publicApi;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function __invoke($search)
    {
        $order = Order::query()->where('corrios_tracking_code',$search)->orWhere('id',$search)->first(['status']);
        if($order){
            if($order->status == Order::STATUS_PREALERT_TRANSIT){
                return apiResponse(true,'PARCEL_TRANSIT');
            }
            if($order->status == Order::STATUS_PREALERT_READY){
                return apiResponse(true,'PARCEL_READY');
            }
            if($order->status == Order::STATUS_CONSOLIDATOIN_REQUEST){
                return apiResponse(true,'CONSOLIDATOIN_REQUEST');
            }
            if($order->status == Order::STATUS_CONSOLIDATED){
                return apiResponse(true,'CONSOLIDATED');
            }
            if($order->status == Order::STATUS_ORDER){
                return apiResponse(true,'ORDER');
            }
            if($order->status == Order::STATUS_NEEDS_PROCESSING){
                return apiResponse(true,'NEEDS_PROCESSING');
            }
            if($order->status == Order::STATUS_CANCEL){
                return apiResponse(true,'CANCEL');
            }
            if($order->status == Order::STATUS_REJECTED){
                return apiResponse(true,'REJECTED');
            }
            if($order->status == Order::STATUS_RELEASE){
                return apiResponse(true,'RELEASE');
            }
            if($order->status == Order::STATUS_REFUND){
                return apiResponse(true,'REFUND');
            }
            if($order->status == Order::STATUS_PAYMENT_PENDING){
                return apiResponse(true,'PAYMENT_PENDING');
            }
            if($order->status == Order::STATUS_PAYMENT_DONE){
                return apiResponse(true,'PAYMENT_DONE');
            }
            if($order->status == Order::STATUS_SHIPPED){
                return apiResponse(true,'SHIPPED');
            }
        }
        return apiResponse(false,'Order not found');
    }
}
