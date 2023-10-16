<?php

namespace App\Http\Controllers\Api\publicApi;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\OrderTrackingRepository;
use App\Http\Resources\OrderTrackingResource;
use App\Services\Correios\Services\Brazil\CorreiosTrackingService;

class OrderTrackingController extends Controller
{
    public $trackings;

    public function __invoke(Request $request, $search, $format = 'json')
    {
        $order_tracking_repository = new OrderTrackingRepository($search);
        $responses = $order_tracking_repository->handle();

        if ($format === 'xml') {
            $code = Order::where('warehouse_number', $search)->value('corrios_tracking_code');
            $orderTrackingService = new CorreiosTrackingService();
            $apiResponse = $orderTrackingService->getTracking($code);
            $xmlResponse = $this->generateXmlResponse($responses, $search, $apiResponse);
            return response($xmlResponse, 200)->header('Content-Type', 'application/xml');
        } else {
            return $this->generateJsonResponse($responses);
        }
    }

    private function generateXmlResponse($responses, $search, $apiResponse)
    {
        // dd($apiResponse);
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
    
        $packageTrackingInfo->addChild('TrackingNumber', $data->trackings[0]->order->warehouse_number);

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
        $iteration = 0;
        foreach (array_reverse($data->trackings) as $event) {
            $trackingEventDetail = $trackingEventHistory->addChild('TrackingEventDetail');
            $trackingEventDetail->addChild('EventStatus', $event->status_code);
            $trackingEventDetail->addChild('EventReason', $event->description);
            $trackingEventDetail->addChild('EventDateTime', substr($event->created_at, 0, -8));
            $eventLocation = $trackingEventDetail->addChild('EventLocation');
            $eventLocation->addChild('City', $event->city);
            $eventLocation->addChild('StateProvince', 'FL');
            $eventLocation->addChild('PostalCode', '33182');
            $eventLocation->addChild('CountryCode', $event->country);

            if ($iteration === 0) {
                $trackingEventDetail->addChild('AdditionalLocationInfo', '');  
                $trackingEventDetail->addChild('SignedForByName', optional(optional($event->order)->recipient)->first_name.' '.optional(optional($event->order)->recipient)->last_name);
            } else {
                $trackingEventDetail->addChild('EstimatedDeliveryDate', $orderDate->format('Y-m-d'));  
            }
            
            $iteration++;
        }
        
        // API Tracking Events
        if (isset($apiResponse->objetos) && is_array($apiResponse->objetos) && count($apiResponse->objetos) > 0) {
            // dd($apiResponse);
            foreach ($apiResponse->objetos as $trackingObject) {
                if(isset($trackingObject->eventos) && is_array($trackingObject->eventos) && count($trackingObject->eventos) > 0) {
                    foreach($trackingObject->eventos as $evento) {
                        
                        $detalhe = optional($evento)->detalhe;
                        $parts = explode('&#13;', $detalhe);
                        $detalheCleaned = trim($parts[0]);

                        $eventStatus = $this->translateDescricaoToEnglish(optional($evento)->descricao);
                        $eventReason = $this->translateDetalheToEnglish($detalheCleaned);
                        $trackingEventDetail = $trackingEventHistory->addChild('TrackingEventDetail');
                        $trackingEventDetail->addChild('EventStatus', $eventStatus);
                        $trackingEventDetail->addChild('EventReason', $eventReason);
                        $trackingEventDetail->addChild('EventDateTime', optional($evento)->dtHrCriado);
                        $eventLocation = $trackingEventDetail->addChild('EventLocation');
                        $eventLocation->addChild('City', optional(optional(optional($evento)->unidade)->endereco)->cidade);
                        $eventLocation->addChild('StateProvince', optional(optional(optional($evento)->unidade)->endereco)->uf);
                        $eventLocation->addChild('PostalCode', optional(optional($data->trackings[0]->order)->recipient)->zipcode);
                        $eventLocation->addChild('CountryCode', "BR");
                    }
                }
            }       
        }

        $xmlString = $xml->asXML();

        return $xmlString;
    }

    public function translateDescricaoToEnglish($descricao) {
        $translations = [
            'Objeto entregue ao destinatário' => 'Object delivered to recipient',
            'Objeto saiu para entrega ao destinatário' => 'Object out for delivery to recipient',
            'Objeto em trânsito - por favor aguarde' => 'Object in transit - please wait',
            'Pagamento confirmado' => 'Payment confirmed',
            'Fiscalização aduaneira concluída - aguardando pagamento' => 'Customs inspection completed - awaiting payment',
            'Encaminhado para fiscalização aduaneira' => 'Forwarded to customs inspection',
            'Objeto recebido pelos Correios do Brasil' => 'Object received by the Brazilian Post Office',
            'Objeto postado' => 'Object Posted',
        ];
    
        return $translations[$descricao] ?? $descricao;
    }

    public function translateDetalheToEnglish($detalhe) {
        $translations = [
            'Aguardando envio para o cliente' => 'Awaiting shipment to customer',
            'Fiscalização aduaneira concluída - aguardando pagamento' => 'Customs inspection completed - awaiting payment',
        ];
    
        return $translations[$detalhe] ?? $detalhe;
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
