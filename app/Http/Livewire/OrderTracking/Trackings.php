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
        // dd($tracking, $hdTrackings);
        foreach($tracking as $tracking) {
            // dd($tracking->codigo);
            if ($tracking->codigo == 'PAR') {
                return 90;
            }

            if ($tracking->codigo == 'PAR') {
                return 100;
            }

            if ($tracking->codigo == 'DO' || $tracking->codigo == 'RO') {
                return 110;
            }

            if ($tracking->codigo == 'OEC') {
                return 120;
            }

            if ($tracking->codigo == 'BDEBDIBDR' || $tracking->codigo == 'BDI') {
                return 130;
            }

            if ($tracking->codigo == 'BDE') {
                return 140;
            }

            if ($tracking->codigo == 'PO') {
                $lastTracking = $hdTrackings->last();

                $todayDate = date('Y-m-d');
                $lastTrackingDate = $lastTracking->created_at;

                $difference = Carbon::parse($todayDate)->diffInDays(Carbon::parse($lastTrackingDate));
                
                if ($difference > 2 && optional(optional(optional($tracking)->unidade)->endereco)->cidade) {
                    return 140;
                }

                return 90;
            }
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

    public function toggleTotalExpressStatus($tracking)
    {
        $lastTrack = last($tracking);
        $status = $lastTrack['code'];
        if ($status == 100) {
            return 80;
        }

        if ($status == 110) {
            return 90;
        }

        if ($status == 120) {
            return 100;
        }

        if ($status == 130) {
            return 110;
        }

        if ($status == 140) {
            return 120;
        }
    }

}
