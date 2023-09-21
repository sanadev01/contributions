<?php

namespace App\Http\Controllers\Api\publicApi;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\OrderTrackingRepository;
use App\Http\Resources\OrderTrackingResource;
class OrderTrackingController extends Controller
{
    public $trackings;

    public function __invoke($search)
    {
        $order_tracking_repository = new OrderTrackingRepository($search);
        $responses = $order_tracking_repository->handle();
        foreach($responses as $response){
            if( $response['success'] == true ){
                if($response['service'] == 'Correios_Chile')
                {
                    $this->trackings = $this->getChileTrackings($response['chile_trackings'], $response['trackings']);
     
                    
                    return apiResponse(true,'Order found', ['hdTrackings'=> OrderTrackingResource::collection($this->trackings), 'apiTrackings' => null ]);
                }
                if($response['service'] == 'USPS')
                {
                    $this->trackings = $this->getUSPSTrackings($response['usps_trackings'], $response['trackings']);
     
                    
                    return apiResponse(true,'Order found',['hdTrackings'=> OrderTrackingResource::collection($this->trackings), 'apiTrackings' => null ]);
                }
                if($response['service'] == 'Correios_Brazil')
                {
                    $this->trackings = $response['trackings'];
                    $apiTracking = $response['api_trackings']; 
                    return apiResponse(true,'Order found',['hdTrackings'=> OrderTrackingResource::collection($this->trackings), 'apiTrackings' => $apiTracking]); 
                }
                
                $this->trackings = $response['trackings'];
                return apiResponse(true,'Order found',['hdTrackings'=> OrderTrackingResource::collection($this->trackings), 'apiTrackings' => null]);
            }
        }
        
        return apiResponse(false,'Order not found', ['hdTrackings'=> $this->trackings, 'apiTrackings' => null ]);
        
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

    private function getUSPSTrackings($response, $hd_trackings)
    {
        $response = array_reverse($response);
        
        foreach($response as $data)
        {
            $hd_trackings->push($data);
        }

        return $hd_trackings;
    }
}
