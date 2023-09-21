<?php
namespace App\Services\GSS\Services;

use Carbon\Carbon;
use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
 
class Parcel { 

   protected $chargableWeight;

   public function getRequestBody($order) {

      if($order->shippingService->service_sub_class == ShippingService::GSS_PMI) {
         $type = 'PMI';
      } elseif($order->shippingService->service_sub_class == ShippingService::GSS_EPMEI) {
         $type = 'EPMEI';
      } elseif($order->shippingService->service_sub_class == ShippingService::GSS_EPMI) {
         $type = 'EPMI';
      } elseif($order->shippingService->service_sub_class == ShippingService::GSS_FCM) {
         $type = 'FCM';
      } elseif($order->shippingService->service_sub_class == ShippingService::GSS_EMS) {
         $type = 'EMS';
      } 

      $refNo = $order->customer_reference;
      $packet = [
               'labelFormat' => 'PDF',
               //Sender Information
               'senderAddress' => [
                  'firstName' => $order->sender_first_name,
                  'lastName' => $order->sender_last_name,
                  'addressLine1' => ($order->sender_address) ? $order->sender_address: '2200 NW 129TH AVE',
                  // 'addressIsPOBox' => true,
                  'city' => ($order->sender_city) ? $order->sender_city: 'Miami',
                  'province' => ($order->sender_state_id) ? $order->senderState->code: 'FL',
                  'postalCode' => ($order->sender_zipcode) ? $order->sender_zipcode: '33182',
                  'countryCode' => 'US',
                  'phone' => ($order->sender_phone) ? $order->sender_phone: '',
                  'email' => ($order->sender_email) ? $order->sender_email: '',
                  'taxpayerID' => '',
                  'customerReferenceID' => ($refNo ? $refNo : $order->tracking_id).' HD-'.$order->id,
               ],
               //Recipient Information
               'recipientAddress' => [
                  'firstName' => $order->recipient->first_name,
                  'lastName' => $order->recipient->last_name,
                  'addressLine1' => $order->recipient->address,
                  'addressLine2' => optional($order->recipient)->address2,
                  'addressLine3' => optional($order->recipient)->street_no,
                  // 'addressIsPOBox' => true,
                  'city' => $order->recipient->city,
                  'province' => $order->recipient->State->code,
                  'postalCode' => cleanString($order->recipient->zipcode),
                  'countryCode' => $order->recipient->country->code,
                  'phone' => ($order->recipient->phone) ? $order->recipient->phone: '',
                  'email' => ($order->recipient->email) ? $order->recipient->email: '',
                  'taxpayerID' => ($order->recipient->tax_id) ? $order->recipient->tax_id: ''
               ],
               //Package Information
               'package' => [
                  'mailingAgentID' => 'HERCOFRGTUSM',
                  'packageID' => $order->getChangeIdAttribute(),
                  'serviceType' => 'LBL',
                  'rateType' => $type,
                  'itemValueCurrencyType' => 'USD',
                  // 'valueLoaded' => true,
                  'packageType' => 'G',
                  'weight' => $order->weight,
                  'weightUnit' => ($order->measurement_unit == "lbs/in") ? 'LB' : 'KG',
                  // 'returnServiceRequested' => true,
                  'orderID' => "$order->id",
                  'orderDate' => Carbon::today()->toDateString(),
                  // 'paymentAndDeliveryTerms' => 'COD',
                  'pfCorEEL' => 'X20230101123456',
               ]+($type != "FCM" ? [
                  'length' => $order->length,
                  'width' => $order->width,
                  'height' => $order->height,
                  'unitOfMeasurement' => ($order->measurement_unit == "lbs/in") ? 'IN' : 'CM',
                  ]:[]),
               //Items Information
               'items' => $this->setItemsDetails($order),              
            ];
      return $packet;
   }

   private function setItemsDetails($order)
   {
        $items = [];
      
        if (count($order->items) >= 1) {
         $totalQuantity = $order->items->sum('quantity');
            foreach ($order->items as $key => $item) {
                $itemToPush = [];
                $originCountryCode = optional($order->senderCountry)->code;
                $itemToPush = [
                     'itemID' => "$key",
                     'itemDescription' => $item->description,
                     "customsDescription" => $item->description,
                     'quantity' => (int)$item->quantity,
                     'htsNumber' => "$item->sh_code",
                     'unitValue' => $item->value,
                     'weight' => round($order->weight / $totalQuantity, 2) - 0.02,
                     "weightUnit" => ($order->measurement_unit == "lbs/in") ? 'LB' : 'KG',
                ];
               array_push($items, $itemToPush);
            }
        }
        return $items;
   }


}