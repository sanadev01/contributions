<?php

namespace App\Http\Controllers\Api\Order;

use App\Models\Order;
use App\Models\Region;
use App\Models\Address;
use App\Facades\USPSFacade;
use Illuminate\Http\Request;
use FlyingLuscas\Correios\Client;
use App\Http\Controllers\Controller;
use App\Services\Colombia\ColombiaPostalCodes;

class RecipientController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
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

    public function zipcode(Request $request)
    {
        $correios = new Client;
        $response = $correios->zipcode()->find($request->zipcode);
        
        if(optional($response)['error']){
            return apiResponse(false,'zip code not found / CEP não encontrado');
        }
        return apiResponse(true,'Zipcode success',$response);
    }

    public function colombiaZipcode(Request $request)
    {
        //$zipcode = Region::query()->where("country_id",$request->country_id)->where('name', 'LIKE', "%{$request->city}%")->value('code');
        $colombiaPostalCodeService = new ColombiaPostalCodes();
        $zipCode = $colombiaPostalCodeService->getZipCodes($request->city);
        $data = ['zipCode' => $zipCode];
        return response()->json($data);
    }

    public function validate_USAddress(Request $request)
    {
        return USPSFacade::validateAddress($request);
    }
}
