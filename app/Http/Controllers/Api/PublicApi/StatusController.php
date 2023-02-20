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
        if($order && Auth::id() ==$order->user_id) {  
            return apiResponse(true, "Your Parcel Status is", $order->status);
        } 
        return apiResponse(false,['error' => "Your Parcel doesn't exists."]);

    }

}