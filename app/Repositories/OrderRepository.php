<?php

namespace App\Repositories;

use App\Models\HandlingService;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OrderRepository extends Model
{
    public function updateSenderAddress(Request $request, Order $order)
    {
        $order->update([
            'sender_first_name' => $request->first_name,
            'sender_last_name' => $request->last_name,
            'sender_email' => $request->email,
            'sender_taxId' => $request->phone,
        ]);

        return $order;
    }

    public function updateRecipientAddress(Request $request, Order $order)
    {
        $order->update([
            'recipient_address_id' => $request->address_id
        ]);

        if ( $order->recipient ){

            $order->recipient()->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'city' => $request->city,
                'street_no' => $request->street_no,
                'address' => $request->address,
                'address2' => $request->address2,
                'account_type' => $request->account_type,
                'tax_id' => $request->tax_id,
                'zipcode' => $request->zipcode,
                'state_id' => $request->state_id,
                'country_id' => $request->country_id,
            ]);

            return $order->recipient;

        }

        $order->recipient()->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'city' => $request->city,
            'street_no' => $request->street_no,
            'address' => $request->address,
            'address2' => $request->address2,
            'account_type' => $request->account_type,
            'tax_id' => $request->tax_id,
            'zipcode' => $request->zipcode,
            'state_id' => $request->state_id,
            'country_id' => $request->country_id,
        ]);


        return $order->recipient;
    }

    public function updateHandelingServices(Request $request, Order $order)
    {
        $order->services()->delete();

        foreach($request->get('services',[]) as $serviceId){
            $service = HandlingService::find($serviceId);

            if (!$service ) continue;

            $order->services()->create([
                'service_id' => $service->id,
                'name' => $service->name,
                'cost' => $service->cost,
                'price' => $service->price,
            ]);
        }

        return true;
    }

    public function createConsolidationRequest(Request $request, Order $order)
    {

    }

    public function updateShippingAndItems(Request $request, Order $order)
    {

    }
    

}
