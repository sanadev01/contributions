<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;

class RecipientController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $address = Address::find($request->address_id);
        if ( !$address ){
            return apiResponse(false,"Address Not found");
        }

        $order = Order::find($request->order_id);

        $order->update([
            'recipient_address_id' => $address->id
        ]);
        
        if ( $order->recipient ){

            $order->recipient()->update([
                'first_name' => $address->first_name,
                'last_name' => $address->last_name,
                'email' => $address->email,
                'phone' => $address->phone,
                'city' => $address->city,
                'street_no' => $address->street_no,
                'address' => $address->address,
                'address2' => $address->address2,
                'account_type' => $address->account_type,
                'tax_id' => $address->tax_id,
                'zipcode' => $address->zipcode,
                'state_id' => $address->state_id,
                'country_id' => $address->country_id,
            ]);

            return apiResponse(true,'Address Updated');

        }

        $order->recipient()->create([
            'first_name' => $address->first_name,
            'last_name' => $address->last_name,
            'email' => $address->email,
            'phone' => $address->phone,
            'city' => $address->city,
            'street_no' => $address->street_no,
            'address' => $address->address,
            'address2' => $address->address2,
            'account_type' => $address->account_type,
            'tax_id' => $address->tax_id,
            'zipcode' => $address->zipcode,
            'state_id' => $address->state_id,
            'country_id' => $address->country_id,
        ]);

        return apiResponse(true,'Address Updated');
    }
}
