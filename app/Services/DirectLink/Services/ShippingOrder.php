<?php
namespace App\Services\DirectLink\Services;
 

class ShippingOrder{ 

  public function getRequestBody(){
    return [ 
            "referenceNo"=>"TEST2021112201",
            "referenceNo1"=>"2021112201",
            "trackingNo"=>"",
            "serviceCode"=>"UBI.CN2CA.Purolator.WMT",
            "incoterm"=>"",
            "description"=>"smartwristband",
            "nativeDescription"=>"智能手环",
            "weight"=>0.02,
            "weightUnit"=>"KG",
            "length"=>50,
            "width"=>40,
            "height"=>30,
            "volume"=>0.06,
            "dimensionUnit"=>"CM",
            "invoiceValue"=>1,
            "invoiceCurrency"=>"EUR",
            "pickupType"=>"",
            "authorityToLeave"=>"",
            "lockerService"=>"",
            "batteryType"=>"Lithium Ion Polymer",
            "batteryPacking"=>"Inside Equipment",
            "dangerousGoods"=>"false",
            "serviceOption"=>"",
            "instruction"=>"",
            "facility"=>"",
            "platform"=>"",
            "recipientName"=>"RohitPatel",
            "recipientCompany"=>"RohitPatel",
            "phone"=>"0433813492",
            "email"=>"prohit9@yahoo.com",
            "addressLine1"=>"71 Clayhill Drive ",
            "addressLine2"=>"",
            "addressLine3"=>"",
            "city"=>"Yate, Bristol",
            "state"=>"Yate, Bristol",
            "postcode"=>"BS37 7DA",
            "country"=>"GB",
            "shipperName"=>"",
            "shipperPhone"=>"",
            "shipperAddressLine1"=>"",
            "shipperAddressLine2"=>"",
            "shipperAddressLine3"=>"",
            "shipperCity"=>"",
            "shipperState"=>"",
            "shipperPostcode"=>"",
            "shipperCountry"=>"",
            "returnOption"=>"",
            "returnName"=>"",
            "returnAddressLine1"=>"",
            "returnAddressLine2"=>"",
            "returnAddressLine3"=>"",
            "returnCity"=>"",
            "returnState"=>"",
            "returnPostcode"=>"",
            "returnCountry"=>"",
            "abnnumber"=>"",
            "gstexemptioncode"=>"",
            "orderItems"=>[
               [
                  "itemNo"=>"283856695918",
                  "sku"=>"S8559024940",
                  "description"=>"smartwristband",
                  "nativeDescription"=>"智能手环",
                  "hsCode"=>"",
                  "originCountry"=>"CN",
                  "itemCount"=>"1",
                  "unitValue"=>1,
                  "warehouseNo"=>"",
                  "productURL"=>"",
                  "weight"=>"0.020"
               ]
            ],
            "extendData"=>[
               "agentID"=>"TEST",
               "vendorid"=>"GB123456789",
               "platformorderno"=>"123456",
               "injectPort"=>"YUL"
            ] 
            ];

}
}