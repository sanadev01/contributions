<?php

namespace App\Http\Livewire\Reports;

use App\Models\Order;
use Livewire\Component;
use App\Models\ShippingService;
use App\Models\Warehouse\AccrualRate;

class AnjunReport extends Component
{
    public $order;
    public $isCommission;

    public function mount(Order $order, $isCommission)
    {
        $this->order = $order;
        $this->isCommission = $isCommission;
    }
    public function render()
    {
        return view('livewire.reports.anjun-report');
    }
    public function getValuePaidToCorrieos(Order $order , $isCommission)
    {
        $service  = $order->shippingService->service_sub_class;
        $rateSlab = AccrualRate::getRateSlabFor($order->getOriginalWeight('kg'),$service);

        if ( !$rateSlab ){
            return  0;
        }
        if($isCommission && $service == ShippingService::AJ_Packet_Standard || $service == ShippingService::AJ_Packet_Express){
            return  $rateSlab->commission;
        }
        return $rateSlab->cwb;        
    }
}
