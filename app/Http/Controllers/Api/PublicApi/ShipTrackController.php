<?php

namespace App\Http\Controllers\Api\PublicApi;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\OrderTrackingRepository;
use App\Http\Resources\OrderTrackingResource;
use App\Services\Correios\Services\Brazil\CorreiosTrackingService;

class ShipTrackController extends Controller
{
    public $trackings;

    public function __invoke(Request $request)
    {
        $xmlContent = $request->getContent();
        $xml = simplexml_load_string($xmlContent);
        $trackingCode = (string)$xml->TrackingNumber;
        $order = Order::where('warehouse_number', $trackingCode)->first();

        if ($order) {
            $orderTrackingService = new CorreiosTrackingService();
            $apiResponse = $orderTrackingService->getTracking($order->corrios_tracking_code);
            $xmlResponse = $this->generateXmlResponse($order,$apiResponse);
            return response($xmlResponse, 200)->header('Content-Type', 'application/xml');
        }
    }

    private function generateXmlResponse($order, $apiResponse)
    {
        if (count($order->trackings) < 0 ) {
            $errorXml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
                <AmazonTrackingResponse xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="AmazonTrackingResponse.xsd"></AmazonTrackingResponse>');
            $errorXml->addChild('APIVersion', '4.0');
            $errorXml->addChild('ErrorMessage', 'Order Tracking Not Found');
            return $errorXml->asXML();
        }

        $orderDate = Carbon::parse($order->order_date)->addDays(15);
    
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
            <AmazonTrackingResponse xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="AmazonTrackingResponse.xsd"></AmazonTrackingResponse>');
        
        $xml->addChild('APIVersion', '4.0');
    
        $packageTrackingInfo = $xml->addChild('PackageTrackingInfo');
    
        $packageTrackingInfo->addChild('TrackingNumber', $order->warehouse_number);

        // // Add PackageDestinationLocation element
        $packageDestinationLocation = $packageTrackingInfo->addChild('PackageDestinationLocation');
        $packageDestinationLocation->addChild('City', optional(optional($order)->recipient)->city);
        $packageDestinationLocation->addChild('StateProvince', optional(optional(optional($order)->recipient)->state)->code);
        $packageDestinationLocation->addChild('PostalCode', optional(optional($order)->recipient)->zipcode);
        $packageDestinationLocation->addChild('CountryCode', optional(optional(optional($order)->recipient)->country)->code);

        // Add PackageDeliveryDate element
        $packageDeliveryDate = $packageTrackingInfo->addChild('PackageDeliveryDate');
        $packageDeliveryDate->addChild('ScheduledDeliveryDate', $orderDate->format('Y-m-d')); 
        $packageDeliveryDate->addChild('ReScheduledDeliveryDate', $orderDate->addDays(2)->format('Y-m-d')); 

        // Add TrackingEventHistory element
        $trackingEventHistory = $packageTrackingInfo->addChild('TrackingEventHistory');

        // API Tracking Events
        if (isset($apiResponse->objetos) && is_array($apiResponse->objetos) && count($apiResponse->objetos) > 0) {
            foreach ($apiResponse->objetos as $trackingObject) {
                if(isset($trackingObject->eventos) && is_array($trackingObject->eventos) && count($trackingObject->eventos) > 0) {
                    foreach($trackingObject->eventos as $evento) {
                        $translations = $this->mapEvents(optional($evento)->descricao);
                        $eventStatus = $translations['status'] ?? null;
                        $eventReason = $translations['reason'] ?? null;
                        $trackingEventDetail = $trackingEventHistory->addChild('TrackingEventDetail');
                        $trackingEventDetail->addChild('EventStatus', $eventStatus);
                        $trackingEventDetail->addChild('EventReason', $eventReason);
                        $trackingEventDetail->addChild('EventDateTime', optional($evento)->dtHrCriado);
                        $eventLocation = $trackingEventDetail->addChild('EventLocation');
                        $eventLocation->addChild('City', optional(optional(optional($evento)->unidade)->endereco)->cidade);
                        $eventLocation->addChild('StateProvince', optional(optional(optional($evento)->unidade)->endereco)->uf);
                        $eventLocation->addChild('PostalCode', optional(optional($order)->recipient)->zipcode);
                        $eventLocation->addChild('CountryCode', "BR");
                    }
                }
            }       
        }

        // HomeDelivery Tracking Events
        $iteration = 0;
        foreach (array_reverse($order->trackings->toArray()) as $event) {

            $translations = $this->mapEvents(optional($event)['description']);
            $eventStatus = $translations['status'] ?? null;
            $eventReason = $translations['reason'] ?? null;
            $trackingEventDetail = $trackingEventHistory->addChild('TrackingEventDetail');
            $trackingEventDetail->addChild('EventStatus', $eventStatus);
            $trackingEventDetail->addChild('EventReason', $eventReason);
            $trackingEventDetail->addChild('EventDateTime', substr($event['created_at'], 0, -8));
            $eventLocation = $trackingEventDetail->addChild('EventLocation');
            $eventLocation->addChild('City', optional($event)['city']);
            $eventLocation->addChild('StateProvince', 'FL');
            $eventLocation->addChild('PostalCode', '33182');
            $eventLocation->addChild('CountryCode', optional($event)['country']);

            if ($iteration === 0) {
                $trackingEventDetail->addChild('AdditionalLocationInfo', '');  
                $trackingEventDetail->addChild('SignedForByName', optional(optional($order)->recipient)->first_name.' '.optional(optional($order)->recipient)->last_name);
            } else {
                $trackingEventDetail->addChild('EstimatedDeliveryDate', $orderDate->format('Y-m-d'));  
            }
            
            $iteration++;
        }

        $xmlString = $xml->asXML();

        return $xmlString;
    }

    public function mapEvents($descricao) {
        $translations = [

            'Objeto entregue ao destinatário' => ['status' => 'D1', 'reason' => 'NS'],
            'Objeto saiu para entrega ao destinatário' => ['status' => 'OD', 'reason' => 'NS'],
            'Objeto em trânsito - por favor aguarde' => ['status' => 'X6', 'reason' => 'NS'],
            'Pagamento confirmado' => ['status' => 'K1', 'reason' => 'BD'],
            'Fiscalização aduaneira concluída - aguardando pagamento' => ['status' => 'K1', 'reason' => 'BD'],
            'Encaminhado para fiscalização aduaneira' => ['status' => 'K1', 'reason' => 'CA'],
            'Objeto recebido pelos Correios do Brasil' => ['status' => 'L1', 'reason' => 'NS'],
            'Objeto postado' => ['status' => 'O1', 'reason' => 'NS'],

            'Parcel transfered to airline' => ['status' => 'L1', 'reason' => 'NS'],
            'Parcel inside Homedelivery Container' => ['status' => 'O1', 'reason' => 'NS'],
            'Freight arrived at Homedeliver' => ['status' => 'AF', 'reason' => 'NS'],
            'Order Placed' => ['status' => 'XB', 'reason' => 'NS'],


        ];
    
        return $translations[$descricao] ?? null;
    }

}
