<?php

namespace App\Http\Controllers\Admin\Webhooks\Shopify;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Order;
use App\Models\State;
use App\Services\Converters\UnitsConverter;
use Illuminate\Http\Request;

class OrderCreatedController extends Controller
{
    public function __invoke(Request $request )
    {
        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => base64_decode($request->callbackUser),
                'sender_first_name' => optional($request->customer)['first_name'],
                'sender_last_name' => optional($request->customer)['last_name'],
                'sender_email' => optional($request->customer)['email'],
                'sender_phone' => optional($request->customer)['phone'],
                
                'customer_reference' => $request->order_number,
                'merchant' => $request->referring_site,
                'carrier' => '',
                'tracking_id' => '',
                'weight' => UnitsConverter::gramsToKg($request->total_weight),
                'length' => 16,
                'width' => 11,
                'height' => 2,
                'is_shipment_added' => true,
                'measurement_unit' => 'kg/cm',
                'status' => Order::STATUS_PREALERT_READY
            ]);
    
            $order->update([
                'warehouse_number' => $order->getTempWhrNumber()
            ]);
    
            $senderCountry = Country::where('code',optional($request->shipping_address)['country_code'])->first();
            $senderState = State::where('country_id',optional($senderCountry)->id)->where('code',optional($request->shipping_address)['province_code'])->first();
            $order->recipient()->create([
                'first_name' => optional($request->shipping_address)['first_name'],
                'last_name' => optional($request->shipping_address)['last_name'],
                'phone' => optional($request->shipping_address)['phone'],
                'city' => optional($request->shipping_address)['city'],
                'street_no' => optional($request->shipping_address)['street_no'],
                'address' => optional($request->shipping_address)['address1'],
                'address2' => optional($request->shipping_address)['address2'],
                'tax_id' => optional($request->shipping_address)['tax_id'],
                'zipcode' => optional($request->shipping_address)['zip'],
                'state_id' => optional($senderCountry)->id,
                'country_id' => optional($senderState)->id,
            ]);
    
            foreach ($request->get('line_items',[]) as $item) {
    
                $order->items()->create([
                    'sh_code' => '0000',
                    'description' => optional($item)['name'],
                    'quantity' => optional($item)['quantity'],
                    'value' => optional($item)['price'],
                    // 'contains_battery' => optional($item)['dangrous_item'] == 'contains_battery' ? true: false,
                    // 'contains_perfume' => optional($item)['dangrous_item'] == 'contains_perfume' ? true: false,
                    // 'contains_flammable_liquid' => optional($item)['dangrous_item'] == 'contains_flammable_liquid' ? true: false,
                ]);
            }

            DB::commit();

        } catch (\Exception $ex) {
            DB::rollback();
        }
    }
}
