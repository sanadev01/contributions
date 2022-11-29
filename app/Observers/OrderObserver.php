<?php

namespace App\Observers;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

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
        // if ( !$order->isNeedsProcessing() ){
        //     return;
        // }
        
        // if ( $order->getWeight('kg') <=0 ){
        //     return;
        // }

        // $order->doCalculations();
        // if ( $order->total <=0 ){
        //     return;
        // }
        
        // if ( $order->shipping_value <=0 ){
        //     return;
        // }
        // $order->update([ 'status' => Order::STATUS_ORDER ]);
        if($order->status != $order->getOriginal('status')){  
            event (new OrderStatusUpdated($order));
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
        event (new OrderStatusUpdated($order));
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
