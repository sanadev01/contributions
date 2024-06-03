<?php

namespace App\Listeners;
use App\Events\OrderStatusUpdated;
use App\Repositories\OrderCheckoutRepository; 
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderStatusChanged implements ShouldQueue
{
    use InteractsWithQueue;
    private $orderCheckoutRepository;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(OrderCheckoutRepository $orderCheckoutRepository)
    {
        $this->orderCheckoutRepository = $orderCheckoutRepository;
    }

    /**
     * Handle the event.
     *
     * @param  OrderStatusUpdated  $event
     * @return void
     */
    public function handle(OrderStatusUpdated $orderStatusUpdated)
    {
        $order = $orderStatusUpdated->order;
        return  $this->orderCheckoutRepository->orderStatusWebhook($order);
       
    }
}
