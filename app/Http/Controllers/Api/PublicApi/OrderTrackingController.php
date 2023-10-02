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

    public function __invoke(Request $request, $search, $format = 'json')
    {
        $order_tracking_repository = new OrderTrackingRepository($search);
        $responses = $order_tracking_repository->handle();

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
    
        $data = $jsonResponse->getData()->data;
    
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
            <AmazonTrackingResponse xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="AmazonTrackingResponse.xsd"></AmazonTrackingResponse>');
        
        $xml->addChild('APIVersion', '4.0');
    
        $packageTrackingInfo = $xml->addChild('PackageTrackingInfo');
    
        $packageTrackingInfo->addChild('TrackingNumber', $data->hdTrackings[0]->tracking_code);

        // // Add PackageDestinationLocation element
        $packageDestinationLocation = $packageTrackingInfo->addChild('PackageDestinationLocation');
        $packageDestinationLocation->addChild('City', $data->hdTrackings[0]->city);
        $packageDestinationLocation->addChild('StateProvince', $data->hdTrackings[0]->state);
        $packageDestinationLocation->addChild('PostalCode', $data->hdTrackings[0]->zipcode);
        $packageDestinationLocation->addChild('CountryCode', $data->hdTrackings[0]->country);

        // Add PackageDeliveryDate element
        $packageDeliveryDate = $packageTrackingInfo->addChild('PackageDeliveryDate');
        $packageDeliveryDate->addChild('ScheduledDeliveryDate', ''); 
        $packageDeliveryDate->addChild('ReScheduledDeliveryDate', ''); 

        // Add TrackingEventHistory element
        $trackingEventHistory = $packageTrackingInfo->addChild('TrackingEventHistory');

        // Loop through your JSON data and add TrackingEventDetail elements
        // foreach ($data['trackingEvents'] as $event) {
            $trackingEventDetail = $trackingEventHistory->addChild('TrackingEventDetail');
            $trackingEventDetail->addChild('EventStatus', '');
            $trackingEventDetail->addChild('EventReason', '');
            $trackingEventDetail->addChild('EventDateTime', '');
            $eventLocation = $trackingEventDetail->addChild('EventLocation');
            $eventLocation->addChild('City', '');
            $eventLocation->addChild('StateProvince', '');
            $eventLocation->addChild('PostalCode', '');
            $eventLocation->addChild('CountryCode', '');
            $trackingEventDetail->addChild('SignedForByName', ''); 
        // }

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
