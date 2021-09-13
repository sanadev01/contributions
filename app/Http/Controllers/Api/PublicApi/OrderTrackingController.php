<?php

namespace App\Http\Controllers\Api\publicApi;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\OrderTrackingRepository;

class OrderTrackingController extends Controller
{
    public $trackings;

    public function __invoke($search)
    {
        $order_tracking_repository = new OrderTrackingRepository($search);
        $response = $order_tracking_repository->handle();

        if( $response->success == true )
        {
            $this->trackings = $response->trackings->toArray();

            return apiResponse(true,'Order found', $this->trackings);
        }

        return apiResponse(false,'Order not found', $this->trackings);
        
    }
}
