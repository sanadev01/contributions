<?php

namespace App\Http\Livewire\OrderTracking;

use App\Models\Order;
use Livewire\Component;
use App\Facades\CorreosChileFacade;
use App\Repositories\OrderTrackingRepository;

class Trackings extends Component
{
    public $trackingNumber = '';

    public function render()
    {
        return view('livewire.order-tracking.trackings');
    }

    public function trackOrder()
    {
        if ( $this->trackingNumber != null && $this->trackingNumber != '' &&  strlen($this->trackingNumber) >= 12 )
        {
            $order_tracking_repository = new OrderTrackingRepository($this->trackingNumber);
            $trackings = $order_tracking_repository->handle();
            dd( $trackings );
        }

    }


}
