<?php

namespace App\Http\Livewire\OrderTracking;

use App\Models\Order;
use Livewire\Component;
use App\Facades\CorreosChileFacade;
use Illuminate\Support\Facades\Log;
use App\Repositories\OrderTrackingRepository;

class Trackings extends Component
{
    public $trackingNumber = '';
    public $tracking;
    public $trackings;
    public $order;
    public $message;
    public $status = null;
    public $correios_brazil_recieved = false;
    public $correios_chile_recieved = false;
    public $custom_finished = false;
    public $in_transit = false;
    public $left_to_buyer = false;
    public $delivered_to_buyer = false;
    public $delivered = false;
    public $CorreiosChile = false;
    public $CorreiosBrazil = false;
    public $trackingType;
    public $chileTrackings;
    public $uspsTrackings;
    public $uspsService;
    public $upsTrackings;
    public $upsService;

    public $upsRecieved;
    public $arrivedAtUPSFacility;
    public $departedFromUPSFacility;

    public function render()
    {  
        if( isset($this->tracking) && $this->CorreiosChile == false && $this->CorreiosBrazil == false && $this->uspsService == false && $this->upsService == false)
        {
            $this->toggleStatus(); 
        }

        if( isset($this->tracking) && $this->CorreiosBrazil == true )
        {
            $this->toggleBrazilStatus(); 
        }
        
        if( isset($this->tracking) && $this->CorreiosChile == true )
        {
            $this->toggleChileStatus(); 
            
        }

        if( isset($this->tracking) && $this->uspsService == true )
        {
            $this->toggleUspsStatus();
        }

        if( isset($this->tracking) && $this->upsService == true )
        {
            $this->toggleUpsStatus();
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
            \Log::info([$response]);
            $this->CorreiosChile = false;
            $this->uspsService = false;
            $this->CorreiosBrazil = false;
            $this->upsService = false;

            if($response->service == 'Correios_Brazil')
            {
                $this->CorreiosBrazil = true;
                $this->tracking = (Array)$response->brazil_trackings;
                $this->trackings = $response->trackings;
                $this->order = $response->order;
                $this->status   = $response->status;
                $this->message  = null;
                $this->trackingType = 'BL';
                return true;
            }

            if( $response->service == 'Correios_Chile' )
            {
                $this->CorreiosChile = true;
                $this->tracking = last($response->chile_trackings);
                $this->chileTrackings = $response->chile_trackings;
                $this->trackings = $response->trackings;
                $this->order = $response->order;
                $this->status   = $response->status;
                $this->message  = null;
                $this->trackingType = 'CL';
                Log::info($this->CorreiosChile);
                return true;

            }

            if( $response->service == 'USPS' )
            {
                $this->uspsService = true;
                $this->tracking = last($response->usps_trackings);
                $this->uspsTrackings = $response->usps_trackings;
                $this->trackings = $response->trackings;
                $this->order = $response->order;
                $this->status   = $response->status;
                $this->message  = null;
                $this->trackingType = 'USPS';
                return true;
            }

            if ($response->service == 'UPS') {
                $this->upsService = true;
                $this->tracking = last($response->ups_trackings);
                $this->upsTrackings = $response->ups_trackings;
                $this->trackings = $response->trackings;
                $this->order = $response->order;
                $this->status   = $response->status;
                $this->message  = null;
                $this->trackingType = 'UPS';
                return true;
            }
            
            if( $response->success == true && $response->status = 200){
                
                $this->tracking = $response->trackings->last();
                $this->trackings = $response->trackings;
                $this->order = $response->order;
                $this->status   = $response->status;
                $this->message  = null;
                if($this->tracking->type == 'HD')
                {
                    $this->trackingType = 'HD';
                }
                $this->chileTrackings = [];
                $this->uspsTrackings = [];    
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

    private function toggleStatus()
    {   
        $this->correios_brazil_recieved == false;
        $this->custom_finished == false;
        $this->in_transit == false;
        $this->left_to_buyer == false;
        $this->delivered_to_buyer == false;
        $this->delivered == false;


        $this->correios_brazil_recieved = ( $this->tracking->status_code == 16 && $this->tracking->type == 'PAR' ) ? true : false;
        $this->custom_finished = ( $this->tracking->status_code == 17 && $this->tracking->type == 'PAR' ) ? true : false;
        $this->in_transit = ( ($this->tracking->status_code == 01 && $this->tracking->type == 'RO') || ($this->tracking->status_code == 01 && $this->tracking->type == 'DO') ) ? true : false;
        $this->left_to_buyer = ( $this->tracking->status_code == 0 && $this->tracking->type == 'OEC' ) ? true : false;
        $this->delivered_to_buyer = ( $this->tracking->status_code == 01 && $this->tracking->type == 'BDEBDIBDR' ) ? true : false;
        $this->delivered = ( $this->tracking->status_code == 01 && $this->tracking->type == 'PO' ) ? true : false;
        
        return true;
    }

    private function toggleBrazilStatus()
    {
        $this->correios_brazil_recieved == false;
        $this->custom_finished == false;
        $this->in_transit == false;
        $this->left_to_buyer == false;
        $this->delivered_to_buyer == false;
        $this->delivered == false;

        $this->correios_brazil_recieved = ( $this->tracking['status'] == 16 && $this->tracking['tipo'] == 'PAR' ) ? true : false;
        $this->custom_finished = ( $this->tracking['status'] == 17 && $this->tracking['tipo'] == 'PAR' ) ? true : false;
        $this->in_transit = ( ($this->tracking['status'] == 01 && $this->tracking['tipo'] == 'RO') || ($this->tracking['status'] == 01 && $this->tracking['tipo'] == 'DO') ) ? true : false;
        $this->left_to_buyer = ( $this->tracking['status'] == 0 && $this->tracking['tipo'] == 'OEC' ) ? true : false;
        $this->delivered_to_buyer = ( $this->tracking['status'] == 01 && $this->tracking['tipo'] == 'BDEBDIBDR' ) ? true : false;
        $this->delivered = ( $this->tracking['status'] == 01 && $this->tracking['tipo'] == 'BDE') ? true : false;
        
        return true;
    }

    private function toggleChileStatus()
    {
        $this->correios_chile_recieved = false;
        $this->in_transit = false;
        $this->delivered_to_buyer = false;

        $this->correios_chile_recieved = ($this->tracking['Orden'] == 4 ) ? true : false;
        $this->in_transit = ($this->tracking['Orden'] == 6 ) ? true : false;
        $this->delivered_to_buyer = ($this->tracking['Orden'] == 10 ) ? true : false;

    }

    private function toggleUspsStatus()
    {

    }

    private function toggleUpsStatus()
    {
        $this->upsRecieved = false;
        $this->arrivedAtUPSFacility = false;
        $this->departedFromUPSFacility = false;
        $this->delivered = false;

        $this->upsRecieved = ($this->tracking['status']['type'] == 'I' && $this->tracking['status']['code'] == 'OR') ? true : false;
        $this->arrivedAtUPSFacility = ($this->tracking['status']['type'] == 'I' && $this->tracking['status']['code'] == 'AR') ? true : false;
        $this->departedFromUPSFacility = ($this->tracking['status']['type'] == 'I' && $this->tracking['status']['code'] == 'DP') ? true : false;
        $this->delivered = ($this->tracking['status']['type'] == 'D' && $this->tracking['status']['code'] == 'KB') ? true : false;
    }


}
