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
            if($response->service == 'Correios_Chile')
            {
                $this->trackings = $this->getChileTrackings($response->chile_trackings, $response->trackings);

                $this->trackings = $this->trackings->toArray();
                
                return apiResponse(true,'Order found', $this->trackings);
            }
            $this->trackings = $response->trackings->toArray();

            return apiResponse(true,'Order found', $this->trackings);
        }

        return apiResponse(false,'Order not found', $this->trackings);
        
    }

    private function getChileTrackings($response, $hd_trackings)
    {
        $response = array_reverse($response);
        
        foreach($response as $data)
        {

            $hd_trackings->push($data);
        }

        return $hd_trackings;
    }
}
