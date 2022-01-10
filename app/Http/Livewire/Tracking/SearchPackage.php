<?php

namespace App\Http\Livewire\Tracking;

use App\Models\Order;
use Livewire\Component;

class SearchPackage extends Component
{
    public $packagesRows;
    public $tracking = '';
    public $orderStatus = '';
    public $error = '';
    public $order = [];
    public $newOrder = [];

    public function mount()
    {
        $this->packagesRows = old('package',[]);
        $this->tracking  = $this->tracking;
        $this->newOrder  = $this->newOrder;
        $this->orderStatus  = $this->orderStatus;
    }

    public function render()
    {
        return view('livewire.tracking.search-package');
    }

    public function removeRow($index)
    {
        unset($this->packagesRows[$index]);
    }
    
    public function updatedTracking()
    {
        if($this->tracking){
            
            $order = Order::where('corrios_tracking_code', $this->tracking)->orWhere('id', $this->tracking)
            ->orWhere('warehouse_number', $this->tracking)->first();
            $this->order = $order;
            $this->orderStatus = '';
            
            if($this->order){
                
                if($this->order->status == Order::STATUS_CANCEL){
                    $this->orderStatus = 'Order Cancel';
                    return $this->tracking = '';
                }
                
                if($this->order->status == Order::STATUS_REJECTED){
                    $this->orderStatus = 'Order Rejected';
                    return $this->tracking = '';
                }
                
                if($this->order->status == Order::STATUS_RELEASE){
                    $this->orderStatus = 'Order Release';
                    return $this->tracking = '';
                }
                
                array_push($this->packagesRows,[
                    'tracking_code' => $this->tracking,
                    'client' => $this->order->merchant,
                    'dimensions' => $this->order->length . ' x ' . $this->order->width . ' x ' . $this->order->height,
                    'kg' => $this->order->getWeight('kg'),
                    'lbs' => $this->order->getWeight('lbs'),
                    'unit' => $this->order->measurement_unit,
                    'reference' => $this->order->warehouse_number,
                    'recpient' => $this->order->recipient->first_name.' '.$this->order->recipient->last_name,
                    'status' => $this->order->status
                ]);
                    
                array_push($this->newOrder,$this->order);
            }
        }
            
        $this->tracking = '';
    }
}
