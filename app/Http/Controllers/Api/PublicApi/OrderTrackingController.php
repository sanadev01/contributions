<?php

namespace App\Http\Controllers\Api\publicApi;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\OrderTrackingRepository;
use App\Http\Resources\OrderTrackingResource;

class OrderTrackingController extends Controller
{
    public $trackings;

    public function __invoke(Request $request, $search, $format = 'json')
    {
        $order_tracking_repository = new OrderTrackingRepository($search);
        $responses = $order_tracking_repository->handle();
        // dd($responses);
        if ($format === 'xml') {
            $xmlResponse = $this->generateXmlResponse($responses);
            return response($xmlResponse, 200)->header('Content-Type', 'application/xml');
        } else {
            return $this->generateJsonResponse($responses);
        }
    }

    private function generateXmlResponse($responses)
    {
        $jsonResponse = $this->generateJsonResponse($responses);
        if ($jsonResponse === null || !$jsonResponse->getData()->success) {
            $errorXml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
                <AmazonTrackingResponse xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="AmazonTrackingResponse.xsd"></AmazonTrackingResponse>');
            $errorXml->addChild('APIVersion', '4.0');
            $errorXml->addChild('ErrorMessage', 'Order not found');
            return $errorXml->asXML();
        }
        // dd($jsonResponse->getData());
        $response = json_decode($responses);
        $data = array_shift($response);
        $orderDate = Carbon::parse($data->trackings[0]->order->order_date)->addDays(15);
        // dd($data);
    
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
            <AmazonTrackingResponse xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="AmazonTrackingResponse.xsd"></AmazonTrackingResponse>');
        
        $xml->addChild('APIVersion', '4.0');
    
        $packageTrackingInfo = $xml->addChild('PackageTrackingInfo');
    
        $packageTrackingInfo->addChild('TrackingNumber', $data->trackings[0]->order->corrios_tracking_code);

        // // Add PackageDestinationLocation element
        $packageDestinationLocation = $packageTrackingInfo->addChild('PackageDestinationLocation');
        $packageDestinationLocation->addChild('City', optional(optional($data->trackings[0]->order)->recipient)->city);
        $packageDestinationLocation->addChild('StateProvince', optional(optional(optional($data->trackings[0]->order)->recipient)->state)->code);
        $packageDestinationLocation->addChild('PostalCode', optional(optional($data->trackings[0]->order)->recipient)->zipcode);
        $packageDestinationLocation->addChild('CountryCode', optional(optional(optional($data->trackings[0]->order)->recipient)->country)->code);

        // Add PackageDeliveryDate element
        $packageDeliveryDate = $packageTrackingInfo->addChild('PackageDeliveryDate');
        $packageDeliveryDate->addChild('ScheduledDeliveryDate', $orderDate->format('Y-m-d')); 
        $packageDeliveryDate->addChild('ReScheduledDeliveryDate', $orderDate->addDays(2)->format('Y-m-d')); 

        // Add TrackingEventHistory element
        $trackingEventHistory = $packageTrackingInfo->addChild('TrackingEventHistory');

        // HomeDelivery Tracking Events
        foreach (array_reverse($data->trackings) as $event) {
            $trackingEventDetail = $trackingEventHistory->addChild('TrackingEventDetail');
            $trackingEventDetail->addChild('EventStatus', $event->status_code);
            $trackingEventDetail->addChild('EventReason', $event->description);
            $trackingEventDetail->addChild('EventDateTime', Carbon::parse($event->created_at)->setTimezone('Asia/Tokyo')->format('c'));
            $eventLocation = $trackingEventDetail->addChild('EventLocation');
            $eventLocation->addChild('City', $event->city);
            $eventLocation->addChild('StateProvince', 'FL');
            $eventLocation->addChild('PostalCode', '33182');
            $eventLocation->addChild('CountryCode', $event->country);
            $trackingEventDetail->addChild('SignedForByName', optional(optional($event->order)->recipient)->first_name.' '.optional(optional($event->order)->recipient)->last_name); 
        }
        
        // API Tracking Events

        $xmlString = $xml->asXML();

        return $xmlString;
    }


    private function generateJsonResponse($responses)
    {
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
