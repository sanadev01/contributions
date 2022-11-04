<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        //
    }

    /**
     * Handle the order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        if ( !$order->isNeedsProcessing() ){
            return;
        }
        
        if ( $order->getWeight('kg') <=0 ){
            return;
        }

        $order->doCalculations();
        if ( $order->total <=0 ){
            return;
        }

        if ( $order->shipping_value <=0 ){
            return;
        }

        $status = $order->update([ 'status' => Order::STATUS_ORDER ]);
        $changes = array_diff($status->getOriginal(), $status->getAttributes());
            if(array_key_exists('status',$changes)){

                $client = new Client([
                    'base_uri' => "URI",
                    'headers' => [
                        'Authorization: Basic' => "Token Here",
                    ]
                ]);
        
                try {

                    $webhookResponse = $client->post('clientAdress',[
                        'json' => [
                            'webhook' => [
                                'topic' => 'orders/status',
                                'status' => "Your Parcel Status is".''. Order::STATUS_ORDER,
                                // 'address' => route('admin.webhooks.orderstatus.parcel.status',['callbackUser'=> base64_encode(Auth::id()),'connectId'=> base64_encode($connect->id)]),
                                'address' => 'https://3fe3231b56e7.ngrok.io/webhooks/parcelstatus/webhook/parcel/status?callbackUser='.base64_encode(Auth::id()),
                                'format' => 'json',
                            ]
                        ]
                    ]);
        
                } catch (\Exception $th) {
                    abort(400,'Bad Request'.$th->getMessage());
                }
        
                return json_decode($webhookResponse->getBody()->getContents());
            }

    }

    /**
     * Handle the order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the order "restored" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the order "force deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
