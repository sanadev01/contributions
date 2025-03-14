<?php

namespace App\Http\Controllers\Api\publicApi;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\OrderTrackingRepository;

class TrackingController extends Controller
{
    public $trackings;

    public function __invoke(Request $request)
    {
        $orderTrackingRepository = new OrderTrackingRepository($search = null);
        $this->trackings = $orderTrackingRepository->getTrackings($request);
            if(count($this->trackings))
            {
                return apiResponse(true,'Trackings Found',['hdTrackings'=> $this->trackings]); 
            }

        return apiResponse(false,'Tracking not found', ['hdTrackings'=> $this->trackings]);
        
    }
}
