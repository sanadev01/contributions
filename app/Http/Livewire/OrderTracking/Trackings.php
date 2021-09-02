<?php

namespace App\Http\Livewire\OrderTracking;

use App\Models\Order;
use Livewire\Component;
use App\Facades\CorreosChileFacade;
use App\Repositories\OrderTrackingRepository;

class Trackings extends Component
{
    public $trackingNumber = '';
    public $tracking;
    public $trackings;
    public $message;
    public $status = null;

    public function render()
    {   
        return view('livewire.order-tracking.trackings',[
            'tracking'  => $this->tracking,
            'trackings'  => $this->trackings,
            'status'    => $this->status,
            'message'   => $this->message,
        ]);
    }

    public function trackOrder()
    {
        if ( $this->trackingNumber != null && $this->trackingNumber != '' &&  strlen($this->trackingNumber) >= 12 )
        {
            $this->status = null;
            $this->tracking = null;
            $order_tracking_repository = new OrderTrackingRepository($this->trackingNumber);
            $response = $order_tracking_repository->handle();
            
            if( $response->success == true && $response->status = 200){
                
                $this->tracking = $response->trackings->last();
                $this->trackings = $response->trackings;
                $this->status   = $response->status;
                $this->message  = null;
            }
            if( $response->success == false &&  $response->status == 201){
                $this->status   = $response->status;
                $this->message  = 'Order Under Process';
            }
            if( $response->success == false &&  $response->status == 404){
                $this->status   = $response->status;
                $this->message  = 'Order Not Found';
            }

        }

    }


}
