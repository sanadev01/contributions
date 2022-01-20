<?php

namespace App\Services\USPS;

use App\Models\ShippingService;

class ConsolidatedOrderService
{

    public function makeRequestForSenderRates($order, $request)
    {
        return [
            'from_address' => [
                'company_name' => 'HERCO SUIT#100',
                'first_name' => ($request->first_name) ? $request->first_name : '',
                'last_name' => ($request->last_name) ? $request->last_name.' '.$order['user']['pobox_number'] : '',
                'line1' => $request->sender_address,
                'city' => $request->sender_city,
                'state_province' => $request->sender_state,
                'postal_code' => $request->sender_zipcode,
                'phone_number' => ($order['user']['phone']) ? $order['user']['phone'] : '+13058885191',
                'sms' => ($order['user']['phone']) ? $order['user']['phone'] :'+17867024093',
                'email' => ($order['user']['email']) ? $order['user']['email'] : 'homedelivery@homedeliverybr.com',
                'country_code' => 'US',
            ],
            'to_address' => [
                'company_name' => 'HERCO SUITE#100',
                'line1' => '2200 NW 129TH AVE',
                'city' => 'Miami',
                'state_province' => 'FL',
                'postal_code' => '33182',
                'phone_number' => '+13058885191',
                'country_code' => 'US', 
            ],
            'weight' => (float)$order['weight'],
            'weight_unit' => 'kg',
            'image_format' => 'pdf',
            'usps' => [
                'shape' => 'Parcel',
                'mail_class' => ($request->service == ShippingService::USPS_FIRSTCLASS || $request->service == 'FirstClass') ? 'FirstClass' : 'Priority',
                'image_size' => '4x6',
            ],
        ];
    }
}
