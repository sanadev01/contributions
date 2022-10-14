<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class StatusController extends Controller
{
    public function __invoke(Order $order)
    {        
        if($order) {  
            return apiResponse(true,['status' => $order->status]);
        }else {
            return apiResponse(false,['error' => "YOur Parcel Cannot be Tracked at this moment. Please contact Customer service"]);
        }
    }

}
