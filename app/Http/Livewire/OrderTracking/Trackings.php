<?php

namespace App\Http\Livewire\OrderTracking;

use Livewire\Component;
use App\Repositories\OrderTrackingRepository;

class Trackings extends Component
{
    public $trackingNumber = '';
    public $tracking;
    public $trackings;
    public $order;
    public $message;
    public $status = null;
    public $apiResponse;

    public function render()
    {  
        return view('livewire.order-tracking.trackings',[
            'tracking'  => $this->tracking,
            'trackings'  => $this->trackings,
            'status'    => $this->status,
            'message'   => $this->message,
            'apiTracking'   =>$this->apiResponse,
        ]);
    }

    public function trackOrder()
    {
        if ( $this->trackingNumber != null && $this->trackingNumber != '' &&  strlen($this->trackingNumber) >= 12 )
        {
            $this->status = null;
            $this->tracking = null;
            $order_tracking_repository = new OrderTrackingRepository($this->trackingNumber);
            $this->apiResponse = $order_tracking_repository->handle();
        }

    }

    public function toggleBrazilStatus($tracking)
    {
        if ($tracking['status'] == 16 && $tracking['tipo'] == 'PAR') {
            return 90;
        }

        if ($tracking['status'] == 17 && $tracking['tipo'] == 'PAR') {
            return 100;
        }

        if ($tracking['status'] == 01 && $tracking['tipo'] == 'RO') {
            return 110;
        }

        if ($tracking['status'] == 0 && $tracking['tipo'] == 'OEC') {
            return 120;
        }

        if ($tracking['status'] == 01 && $tracking['tipo'] == 'BDEBDIBDR') {
            return 130;
        }

        if ($tracking['status'] == 01 && $tracking['tipo'] == 'BDE') {
            return 140;
        }

        if ($tracking['status'] == 01 && $tracking['tipo'] == 'PO') {
            return 140;
        }
    }

    public function toggleChileStatus($tracking)
    {
        if ($tracking['Orden'] == 4) {
            return 90;
        }

        if ($tracking['Orden'] == 6) {
            return 100;
        }

        if ($tracking['Orden'] == 10) {
            return 110;
        }

    }


    public function toggleUpsStatus($tracking)
    {
        if ($this->tracking['status']['type'] == 'I' && $this->tracking['status']['code'] == 'OR') {
            return 90;
        }

        if ($this->tracking['status']['type'] == 'I' && $this->tracking['status']['code'] == 'AR') {
            return 100;
        }

        if ($this->tracking['status']['type'] == 'I' && $this->tracking['status']['code'] == 'DP') {
            return 110;
        }

        if ($this->tracking['status']['type'] == 'D' && $this->tracking['status']['code'] == 'KB') {
            return 120;
        }
    }


}
