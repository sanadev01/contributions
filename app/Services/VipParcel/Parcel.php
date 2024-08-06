<?php

namespace App\Services\VipParcel;

use App\Models\Order;
use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
use DateTime;

class Parcel
{

   protected $order;
   protected $weight;
   protected $width;
   protected $height;
   protected $length;
   protected $token;
   
   protected $chargableWeight;
   public function __construct(Order $order)
   {
      $this->order = $order;
      $this->weight = $order->weight;
      if(!$order->isWeightInKg()) {
         $this->weight = UnitsConverter::poundToKg($order->getOriginalWeight('lbs'));
      }
      $this->weight = number_format($this->weight * 35.274, 2);
      $this->width = round($order->isMeasurmentUnitCm() ? $order->width : UnitsConverter::inToCm($order->width));
      $this->height = round($order->isMeasurmentUnitCm() ? $order->height : UnitsConverter::inToCm($order->height));
      $this->length = round($order->isMeasurmentUnitCm() ? $order->length : UnitsConverter::inToCm($order->length));

      if(app()->isProduction()) {
        $this->token = config('vipparcel.production.token');
      }else{ 
        $this->token = config('vipparcel.test.token');
      }
   }
   public function getRequestBody() {

    if($this->order->shippingService->service_sub_class == ShippingService::VIP_PARCEL_FCP) {
       $mailClass = 'FirstClassPackageInternationalService';
    } elseif($this->order->shippingService->service_sub_class == ShippingService::VIP_PARCEL_PMEI) {
       $mailClass = 'PriorityMailExpressInternational';
    }  elseif($this->order->shippingService->service_sub_class == ShippingService::VIP_PARCEL_PMI) {
       $mailClass = 'PriorityMailInternational';
    }
    $refNo = $this->order->customer_reference;

    $packet = [
             //Reference Information
            'authToken' => $this->token,
            'labelType' => "International",
            'mailClass' => $mailClass,
            'description' => ($refNo ? $refNo : $this->order->tracking_id).' HD-'.$this->order->id,
            'weightOz' => $this->weight,
            //Sender Information
            'sender' => [
                'firstName' => optional($this->order)->sender_first_name,
                'lastName' => optional($this->order)->sender_last_name,
                'streetAddress' => (optional(optional($this->order)->user)->address) ? optional(optional($this->order)->user)->address: '',
                'city' => (optional(optional($this->order)->user)->city) ? optional(optional($this->order)->user)->city: '',
                'postalCode' => (optional(optional($this->order)->user)->zipcode) ? optional(optional($this->order)->user)->zipcode: '',
                'state' => (optional(optional($this->order)->user)->state()->first()->code) ? optional(optional($this->order)->user)->state()->first()->code: '',
                'phone' => (optional(optional($this->order)->user)->phone) ? substr(substr(optional(optional($this->order)->user)->phone, 1), -10): '',
             ],
             //Recipient Information
             'recipient' => [
                'firstName' => optional(optional($this->order)->recipient)->first_name,
                'lastName' => optional(optional($this->order)->recipient)->last_name,
                'phone' => ($this->order->recipient->phone) ? substr($this->order->recipient->phone, 1): '',
                'streetAddress' => $this->order->recipient->address.' '.optional($this->order->recipient)->address2.' '.$this->order->recipient->street_no,
                'city' => $this->order->recipient->city,
                'state' => $this->order->recipient->State->code,
                'postalCode' => cleanString($this->order->recipient->zipcode),
                'countryId' => $this->order->recipient->country->id,
             ],
             //Dimensions
             'dimensionalWeight' => [
                'length' => $this->length,
                'height' => $this->height,
                'width' => $this->width,
             ],
             //Customs Info
             'customsInfo' => [
                'category' => "Gift",
                'taxId' => '',
             ],
             //Items Info
             'customsItem' => $this->setItemsDetails(),
             'validationAddress' => false,
             'imageFormat' => "PDF",         
          ];
    return $packet;
 }

 private function setItemsDetails()
 {
      $items = [];
      if (count($this->order->items) >= 1) {
       $totalQuantity = $this->order->items->sum('quantity');
          foreach ($this->order->items as $key => $item) {
              $itemToPush = [];
              $itemToPush = [
                  'quantity' => (int)$item->quantity,
                  'value' => $item->value,
                   'description' => $item->description,
                   'weightOz' => round($this->weight / $totalQuantity, 2) - 0.02,
              ];
             array_push($items, $itemToPush);
          }
      }
      return $items;
 }
}
