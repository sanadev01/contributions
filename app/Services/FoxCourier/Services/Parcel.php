<?php

namespace App\Services\FoxCourier\Services;

use DateTime;
use App\Models\Order;
use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;

class Parcel
{

   protected $order;
   protected $weight;
   protected $width;
   protected $height;
   protected $length;
   
   protected $chargableWeight;
   public function __construct(Order $order)
   {
      $this->order = $order;
      $this->weight = $order->weight;
      if(!$order->isWeightInKg()) {
         $this->weight = UnitsConverter::poundToKg($order->getOriginalWeight('lbs'));
      }
      $this->width = round($order->isMeasurmentUnitCm() ? $order->width : UnitsConverter::inToCm($order->width));
      $this->height = round($order->isMeasurmentUnitCm() ? $order->height : UnitsConverter::inToCm($order->height));
      $this->length = round($order->isMeasurmentUnitCm() ? $order->length : UnitsConverter::inToCm($order->length));
   }
   public function getRequestBody()
   {
      // Determine the incoterm based on tax modality
      if (strcasecmp($this->order->tax_modality, "DDU") === 0) {
         $incoterm = "DDU";
      } elseif (strcasecmp($this->order->tax_modality, "DDP") === 0) {
         $incoterm = "DDP";
      }

      // Define cdPartnerRepository based on the environment
      $cdPartnerRepository = app()->isProduction() ? 'FOX_API_HERCO_BOPE0' : 'FOX_API_TEST_HERCO_xVDCD';

      // Handle street number for recipient
      $streetNo = optional($this->order->recipient)->street_no;
      if ($streetNo == 0 || $streetNo == '0') {
         $streetNo = null;
      }

      $service_sub_class = $this->order->shippingService->service_sub_class;
      $standard = in_array($service_sub_class, [ShippingService::FOX_ST_COURIER]);
      $tpService = $standard ? 'ST' : 'EX'; 

      // Construct the request body
      $parcel = [
         "cdPartnerRepository" => $cdPartnerRepository,
         "cdShipment" => $this->order->warehouse_number,
         "tpCourierOperation" => 'IMPORT',
         "cdOrigin" => 'USA',
         "tpPPCC" => 'PP',
         "cdDestination" => 'GRU',
         "cdIncoterm" => $incoterm,
         "tpService" => $tpService,
         "vlWeight" => $this->weight,
         "vlDepth" => $this->height,
         "vlWidth" => $this->width,
         "vlHeight" => $this->height,
         "nbQuantity" => 1,
         "taxRegime" => 7,
         "unitMeasure" => '11',

         // Shipper information
         "shipper" => [
               "cdDocument" => $this->order->user->tax_id ? $this->order->user->tax_id : '', // Seller's tax number
               "tpDocument" => "2",
               "name" => $this->order->getSenderFullName(),
               "dsPhoneNumber" => $this->order->sender_phone ?? $this->order->user->phone,
               "dsEmail" => $this->order->sender_email ?? $this->order->user->email,
               "obAddress" => [
                  "dsAddress" => $this->order->sender_address ?? '2200 NW 129TH AVE',
                  "dsComplement" => '',
                  "nmCity" => $this->order->sender_city ?? 'Miami',
                  "cdPostal" => $this->order->sender_zipcode ?? '33182',
                  "cdState" => $this->order->sender_state_id ? $this->order->senderState->code : 'FL',
                  "cdCountry" => 'US'
               ]
         ],

         // Consignee (cnee) information
         "cnee" => [
               "cdDocument" => $this->order->recipient->tax_id,
               "tpDocument" => $this->order->recipient->account_type == "business" ? "2" : "1",
               "name" => $this->order->recipient->getFullName(),
               "dsPhoneNumber" => substr($this->order->recipient->phone, -11),
               "dsEmail" => $this->order->recipient->email ?? '',
               "obAddress" => [
                  "dsAddress" => $this->order->recipient->address,
                  "dsComplement" => optional($this->order->recipient)->address2 ?? '',
                  "nmCity" => $this->order->recipient->city,
                  "cdPostal" => cleanString($this->order->recipient->zipcode),
                  "cdState" => $this->order->recipient->State->code,
                  "cdCountry" => $this->order->recipient->country->code
               ]
         ],

         "dsPayImportTax" => "true", // Need to set it up with PRC if required
         "flConforme" => false, // Need to set it up with PRC if required
         "cdContract" => "CTFOX240501",
         "vlProvisionICMS" => 0,
         "vlProvisionII" => 0,

         // Goods information
         "goods" => $this->setItemsDetails()
      ];

      return [$parcel];
   }

   private function setItemsDetails()
   {
      $items = [];

      if (count($this->order->items) >= 1) {
         $totalQuantity = $this->order->items->sum('quantity');
         foreach ($this->order->items as $item) {
               $itemToPush = [
                  "cdFreightCurrencyGoods" => "USD",
                  "vlUnitaryGoods" => $item->value,
                  "vlFreight" => ((float)$this->order->insurance_value) + ((float)$this->order->user_declared_freight),
                  "vlWeight" => round($this->weight / $totalQuantity, 2) - 0.02,
                  "nbQuantity" => (int)$item->quantity,
                  "dsGoods" => substr($item->description, 0, 20),
                  "cdNCM" => substr($item->sh_code, 0, 6),
                  "cdHsCode" => substr($item->sh_code, 0, 6)
               ];
               array_push($items, $itemToPush);
         }
      }

      return $items;
   }

}
