<?php

namespace App\Http\Livewire\OrderTracking;

use Livewire\Component;
use App\Repositories\OrderTrackingRepository;
use App\Services\Excel\Export\ExportTracking;
use Carbon\Carbon;

class Trackings extends Component
{
    public $trackingNumber = '';
    public $apiResponse;

    public function render()
    {
        return view('livewire.order-tracking.trackings',[
            'trackings'   =>$this->apiResponse,
        ]);
    }

    public function trackOrder()
    {
        if ( $this->trackingNumber != null && $this->trackingNumber != '' &&  strlen($this->trackingNumber) >= 12 )
        {
            $order_tracking_repository = new OrderTrackingRepository($this->trackingNumber);
            $this->apiResponse = $order_tracking_repository->handle();
        }

    }

    public function download()
    {
        if ($this->apiResponse) {
            $exportTracking = new ExportTracking($this->apiResponse);
            return $exportTracking->handle();
        }
    }    

    public function toggleBrazilStatus($tracking, $hdTrackings)
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

        if ($tracking['status'] == 01 && ($tracking['tipo'] == 'BDEBDIBDR' || $tracking['tipo'] == 'BDI')) {
            return 130;
        }

        if ($tracking['status'] == 01 && $tracking['tipo'] == 'BDE') {
            return 140;
        }

        if ($tracking['status'] == 01 && $tracking['tipo'] == 'PO') {
            $lastTracking = $hdTrackings->last();

            $todayDate = date('Y-m-d');
            $lastTrackingDate = $lastTracking->created_at;

            $difference = Carbon::parse($todayDate)->diffInDays(Carbon::parse($lastTrackingDate));
            
            if ($difference > 2 && optional($tracking)['cidade']) {
                return 140;
            }

            return 90;
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
        if ($tracking['status']['type'] == 'I' && $tracking['status']['code'] == 'OR') {
            return 90;
        }

        if ($tracking['status']['type'] == 'I' && $tracking['status']['code'] == 'AR') {
            return 100;
        }

        if ($tracking['status']['type'] == 'I' && $tracking['status']['code'] == 'DP') {
            return 110;
        }

        if ($tracking['status']['type'] == 'D' && $tracking['status']['code'] == 'KB') {
            return 120;
        }
    }
    
    public function togglePrime5Status($tracking)
    {
        $lastTrack = last($tracking);
        $status = $lastTrack['Id'];
        if ($status == '11' || $status >= '14') {
            return 80;
        }

        if ($status == '7' || $status == '4') {
            return 90;
        }

        if ($status == '10') {
            return 100;
        }

        if ($status == '8') {
            return 110;
        }

        if ($status == '9') {
            return 120;
        }
    }

}
