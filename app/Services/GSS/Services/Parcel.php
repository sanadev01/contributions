<?php
namespace App\Services\GSS\Services;

use Carbon\Carbon;
use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
 
class Parcel { 

   protected $chargableWeight;

   public function getRequestBody($order) {

      if($order->shippingService->service_sub_class == ShippingService::GSS_USPS) {
         $type = 'IPA';
      } 
      if($order->isWeightInKg()) {
         $weight = UnitsConverter::kgToGrams($order->getWeight('kg'));
      }else{
            $kg = UnitsConverter::poundToKg($order->getWeight('lbs'));
            $weight = UnitsConverter::kgToGrams($kg);
      }
      $refNo = $order->customer_reference;
      $packet = [
               'labelForamt' => 'PDF',
               //Sender Information
               'senderAddress' => [
                  'name' => "USERNAMEHERE",
                  'firstName' => $order->sender_first_name,
                  'lastName' => $order->sender_last_name,
                  'businessName' => "BusinessNameHERE",
                  'addressLine1' => ($order->sender_address) ? $order->sender_address: '2200 NW 129TH AVE',
                  'addressIsPOBox' => true,
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
                  'name' => "USERNAMEHERE",
                  'firstName' => $order->recipient->first_name,
                  'lastName' => $order->recipient->last_name,
                  'businessName' => "BusinessNameHERE",
                  'addressLine1' => $order->recipient->address.' '.optional($order->recipient)->address2.' '.$order->recipient->street_no,
                  'addressIsPOBox' => true,
                  'city' => $order->recipient->city,
                  'province' => $order->recipient->State->code,
                  'postalCode' => cleanString($order->recipient->zipcode),
                  'countryCode' => $order->recipient->country->code,
                  'phone' => ($order->recipient->phone) ? $order->recipient->phone: '',
                  'email' => ($order->recipient->email) ? $order->recipient->email: '',
                  'taxpayerID' => ($order->recipient->tax_id) ? $order->recipient->tax_id: '',
               ],
               //Package Information
               'package' => [
                  'mailingAgentID' => 'HERCOFRGTUSM',
                  'packageID' => ($refNo ? $refNo : $order->tracking_id).' HD-'.$order->id,
                  'serviceType' => 'LBL',
                  'rateType' => $type,
                  'packagePhysicalCount' => 1,
                  'itemValueCurrencyType' => 'USD',
                  'valueLoaded' => true,
                  'packageType' => 'G',
                  'weight' => $weight,
                  'weightUnit' => ($order->measurement_unit == "lbs/in") ? 'LB' : 'KG',
                  'length' => $order->length,
                  'width' => $order->width,
                  'height' => $order->height,
                  'unitOfMeasurement' => ($order->measurement_unit == "lbs/in") ? 'IN' : 'CM',
                  'returnServiceRequested' => true,
                  'orderID' => $order->id,
                  'orderDate' => Carbon::today()->toDateString(),
                  'invoiceNumber' => '',
                  'insuredAmount' => 0,
                  'shippingAndHandling' => '',
                  'shippingCurrencyType' => 'USD',
                  'intendedShipDate' => '',
                  'paymentAndDeliveryTerms' => 'COD',
                  'pfCorEEL' => '',
                  'postagePaid' => 0.5,
                  'postagePaidCurrencyType' => 'USD',
                  'recipientAddressIsValidated' => true,
               ],
               //Items Information
               'items' => [
                  $this->setItemsDetails($order),
               ],              
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
                     'description' => $item->description,
                     'quantity' => (int)$item->quantity,
                     'htsNumber' => $item->sh_code,
                     'unitValue' => $item->value,
                     'weight' => round($order->weight / $totalQuantity, 2) - 0.02,
                ];
               array_push($items, $itemToPush);
            }
        }
        return $items;
   }


}