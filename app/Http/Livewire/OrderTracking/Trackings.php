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
    public $correios_brazil_recieved = false;
    public $correios_chile_recieved = false;
    public $custom_finished = false;
    public $in_transit = false;
    public $left_to_buyer = false;
    public $delivered_to_buyer = false;
    public $posted = false;
    public $CorreiosChile = false;

    public function render()
    {  
        if( isset($this->tracking) && $this->CorreiosChile == false )
        {
            $this->toggleStatus(); 
        }

        if( isset($this->tracking) && $this->CorreiosChile == true )
        {
            $this->toggleCorreiosChileStatus();
        }
       
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
            
            if($response->service == 'Correios_Chile')
            {
                $this->CorreiosChile = true;
            }
        
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

    public function toggleStatus()
    {        
        $this->correios_brazil_recieved = ( $this->tracking->status_code == 16 && $this->tracking->type == 'PAR' ) ? true : false;
        $this->custom_finished = ( $this->tracking->status_code == 17 && $this->tracking->type == 'PAR' ) ? true : false;
        $this->in_transit = ( ($this->tracking->status_code == 01 && $this->tracking->type == 'RO') || ($this->tracking->status_code == 01 && $this->tracking->type == 'DO') ) ? true : false;
        $this->left_to_buyer = ( $this->tracking->status_code == 0 && $this->tracking->type == 'OEC' ) ? true : false;
        $this->delivered_to_buyer = ( $this->tracking->status_code == 01 && $this->tracking->type == 'BDEBDIBDR' ) ? true : false;
        $this->posted = ( $this->tracking->status_code == 01 && $this->tracking->type == 'PO' ) ? true : false;
        
        return true;
    }

    public function toggleCorreiosChileStatus()
    {
        $this->correios_chile_recieved = ( isset($this->tracking->Orden) == 4 ) ? true : false;
        $this->in_transit = ( isset($this->tracking->Orden) == 6 ) ? true : false;
        $this->delivered_to_buyer = ( isset($this->tracking->Orden) == 10 ) ? true : false;
    }


}
