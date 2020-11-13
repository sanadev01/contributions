<?php

namespace App\Repositories;

use App\Models\Order;
use App\Services\Converters\UnitsConverter;
use App\Services\Leve\Client;
use App\Services\Leve\Models\Address;
use App\Services\Leve\Models\Order as LeveOrder;
use App\Services\Leve\Models\Package;
use App\Services\Leve\Models\Product;
use App\Services\Leve\Models\Recipient;

class LabelRepository
{
    protected $error;

    public function get(Order $order)
    {
        $leveClient = new Client;
        if ( $order->getCN23() ){
            $data = $leveClient->downloadCN23($order->getCN23()->stamp_url);
            if ( $data->success ){
                return $data->data;
            }

            $this->error = $data->message;
            return null;
        }

        return $this->update($order);
    }

    public function update(Order $order)
    {
        $leveClient = new Client;
        $cn23 = $this->generateLabel($order);

        if ( $cn23 ){
            $order->setCN23( (array) $cn23);
            $data = $leveClient->downloadCN23($order->getCN23()->stamp_url);
            if ( $data->success ){
                return $data->data;
            }

            $this->error = $data->message;
            return null;
        }

        return null;
    }

    protected function generateLabel(Order $order)
    {
        $recipientAddress = $order->recipient;

        $width = round($order->isMeasurmentUnitCm() ? $order->width : UnitsConverter::inToCm($order->width));
        $height = round($order->isMeasurmentUnitCm() ? $order->height : UnitsConverter::inToCm($order->height));
        $length = round($order->isMeasurmentUnitCm() ? $order->length : UnitsConverter::inToCm($order->length));

        $leveOrder = new LeveOrder();
        $leveOrder->order_number = $order->warehouse_number;
        $leveOrder->external_reference = $order->customer_reference;
        $leveOrder->purchase_date = $order->created_at->toIso8601String();
        $leveOrder->weight =  round($order->isWeightInKg() ? $order->weight  : UnitsConverter::poundToKg($order->weight),2) ;
        $leveOrder->width =  $width > 11 ? $width : 11;
        $leveOrder->height = $height > 2 ? $height : 2;
        $leveOrder->extent_length = $length > 16 ? $length : 16 ;
        $leveOrder->shipment_value = $order->user_declared_freight ?  round($order->user_declared_freight,2) : round($order->shipping_value,2) ;
        $leveOrder->sender_name = $order->sender_first_name ? "{$order->sender_first_name} {$order->sender_last_name}" : "{$order->user->name} {$order->user->last_name}";
        $leveOrder->mkt_place_name = $order->user->market_place_name;
        
        $hazardousItems = [];
        if ( $order->items()->where('contains_battery',true)->count() > 0 ){
            $hazardousItems = ["UN3481"];
        }
        
        if ( $order->items()->where('contains_perfume',true)->count() > 0 ){
            $hazardousItems = array_merge(["ID8000"],$hazardousItems);
        }

        $leveOrder->hazardous_contents_labels = $hazardousItems;

        $address = new Address();
        $address->number= $recipientAddress->street_no ? $recipientAddress->street_no :'s/n';
        $address->neighborhood= 's/n';
        $address->street= $recipientAddress->address;
        $address->complement= $recipientAddress->address2;
        $address->city= $recipientAddress->city;
        $address->zip_code=  cleanString($recipientAddress->zipcode);
        $address->state_abbreviation= $recipientAddress->state->code;
        $address->country_abbreviation= $recipientAddress->country->code;

        $recipient = new Recipient();
        $recipient->name = "{$recipientAddress->first_name} $recipientAddress->last_name";
        $recipient->email = $recipientAddress->email;
        $recipient->phone_number = $recipientAddress->phone;
        $recipient->registration_number = cleanString( $recipientAddress->tax_id );

        $products = [];
        foreach ( $order->items as $orderItem){
            $item = new Product();
            $item->description = $orderItem->description;
            $item->amount = (int)$orderItem->quantity;
            $item->unit_value = round((float)$orderItem->value,2);
            $item->hs_code = $orderItem->sh_code;
            $products[] = $item;
        }

        $package = new Package();
        $package->order = $leveOrder;
        $package->products = $products;
        $package->recipient = $recipient;
        $package->address = $address;

        $leveClient = new Client;
        $response = $leveClient->createPackage($package);

        if ( !$response->success ){
           $this->error = $response->message;
           return false;

        }

        return $response->data;
    }

    public function getError()
    {
        return $this->error;
    }
}
