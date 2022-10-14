<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class StatusController extends Controller
{
    public function __invoke(Order $id)
    {        
        if($id) {  

            if($id->status == Order::STATUS_PREALERT_TRANSIT) {
                $status = "Your Parcel is in Transit";
            }elseif($id->status == Order::STATUS_PREALERT_READY){
                $status = "Your Parcel is ready to Ship.";
            }elseif($id->status == Order::STATUS_ORDER){
                $status = "Your Parcel is Under Progress";
            }elseif($id->status == Order::STATUS_NEEDS_PROCESSING){
                $status = "Your Parcel is Under Progress";
            }elseif($id->status == Order::STATUS_PAYMENT_PENDING){
                $status = "Your Parcel Payment is Pending";
            }elseif($id->status == Order::STATUS_PAYMENT_DONE){
                $status = "Your Parcel Payment has been Received";
            }elseif($id->status == Order::STATUS_CANCEL) {
                $status = "Your Parcel has been Cancelled";
            }elseif($id->status == Order::STATUS_REJECTED) {
                $status = "Your Parcel has been Rejected";
            }elseif($id->status == Order::STATUS_RELEASE) {
                $status = "Your Parcel has been Released";
            }elseif($id->status == Order::STATUS_REFUND){
                $status = "Your Parcel amount has been Refunded";
            }elseif ($id->status == Order::STATUS_SHIPPED ){
                $status = "Your Parcel has been Shipped to Brazil";
            }else {
                $status ='';
            }
            if($status) {
                return apiResponse(true,['status' => $status]);
            }else {
                return apiResponse(false,['error' => "YOur Parcel Cannot be Tracked at this moment. Please Try Later!"]);
            }

        }
        return apiResponse(false, $error);
    }

}
