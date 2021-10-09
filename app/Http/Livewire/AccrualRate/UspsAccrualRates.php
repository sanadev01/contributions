<?php

namespace App\Http\Livewire\AccrualRate;

use App\Models\Order;
use Livewire\Component;
use App\Services\Excel\Export\USPSAccrualRateExport;

class UspsAccrualRates extends Component
{
    public $start_date;
    public $end_date;
    public $searchOrders;
    public $error;

    public function render()
    {
        return view('livewire.accrual-rate.usps-accrual-rates');
    }

    public function search()
    {
        
        if($this->start_date != null || $this->end_date != null)
        {
            $this->searchOrders = $this->getOrders();
        }
    }

    public function download()
    {
        $orders = $this->getOrders();
        $exportService = new USPSAccrualRateExport($orders);
        return $exportService->handle();
    }

    public function getOrders()
    {
        $orders = Order::where([
                                ['corrios_usps_tracking_code', '!=', null],
                                ['usps_response', '!=', null] 
                        ])->whereBetween('order_date',[$this->start_date.' 00:00:00', $this->end_date.' 23:59:59'])->get()->groupBy('corrios_usps_tracking_code')->all();
        return $orders;                
    }
}
