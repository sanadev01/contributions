<?php

namespace App\Http\Livewire\Label;

use Carbon\Carbon;
use App\Models\Order;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderTracking;
use App\Repositories\LabelRepository;
use Illuminate\Support\Facades\Storage;

class ScanLabel extends Component
{
    public $packagesRows;
    public $tracking = '';
    public $orderStatus = '';
    public $start_date;
    public $end_date;
    public $error = '';
    public $order = [];
    public $searchOrder = [];
    public $newOrder = [];

    public function mount()
    {
        
        $this->packagesRows = old('package',[]);
        $this->tracking  = $this->tracking;
        $this->newOrder  = $this->newOrder;
        $this->orderStatus  = $this->orderStatus;
        $this->searchOrder  = $this->searchOrder;
    }

    public function render()
    {
        return view('livewire.label.scan-label');
    }

    public function addRow()
    {
        array_push($this->packagesRows,[
            'tracking_code' => '',
            'client' => '',
            'dimensions' => '',
            'kg' => '',
            'reference' => '',
            'recpient' => '',
            'order_date' => '',
        ]);
    }

    public function removeRow($index)
    {
        $this->removeOrderTracking($this->packagesRows[$index]['tracking_code']);
        unset($this->packagesRows[$index]);
    }
    
    public function getTrackingCode(string $trackingCode, $index)
    {
        $order = Order::where('corrios_tracking_code', $trackingCode)->first();
        $this->order = $order;
        
        if($this->order){
            $this->packagesRows[$index]['tracking_code'] = $trackingCode;
            $this->packagesRows[$index]['pobox'] = $this->order->user->pobox_number;
            $this->packagesRows[$index]['client'] = $this->order->merchant;
            $this->packagesRows[$index]['dimensions'] = $this->order->length . ' x ' . $this->order->length . ' x ' . $this->order->height ;
            $this->packagesRows[$index]['kg'] = $this->order->getWeight('kg');
            $this->packagesRows[$index]['reference'] = $this->order->id;
            $this->packagesRows[$index]['recpient'] = $this->order->recipient->first_name;
            $this->packagesRows[$index]['order_date'] = $this->order->order_date->format('m-d-Y');
            
            $this->addRow();
        }
    }
    
    public function updatedTracking()
    {
        if($this->tracking){
            
            $order = Order::where('corrios_tracking_code', $this->tracking)->first();
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

                if($this->order->status == Order::STATUS_REFUND){
                    $this->orderStatus = 'Order Refund';
                    return $this->tracking = '';
                }
                
                $newRow = [
                    'tracking_code' => $this->tracking,
                    'pobox' => $this->order->user->pobox_number,
                    'client' => $this->order->merchant,
                    'dimensions' => $this->order->length . ' x ' . $this->order->length . ' x ' . $this->order->height,
                    'kg' => $this->order->getWeight('kg'),
                    'reference' => $this->order->id,
                    'recpient' => $this->order->recipient->first_name,
                    'order_date' => $this->order->order_date->format('m-d-Y'),
                ];
                
                if(in_array($newRow, $this->packagesRows)){
                    $this->orderStatus = 'Order already Existing';
                    return $this->tracking = '';
                }
                
                array_push($this->packagesRows, $newRow);

                array_push($this->newOrder,$this->order);

                $this->addOrderTracking($this->order);
                
                $this->tracking = '';
                $this->orderStatus = '';
                if(Auth::user()->isUser() && Auth::user()->role->name == 'scanner'){
                    if(!$this->order->arrived_date){
                        $this->order->update([
                            'arrived_date' => date('Y-m-d H:i:s'), 
                        ]);
                    }
                }
            }
        }
        $this->tracking = '';
    }

    public function printLabel(LabelRepository $labelRepository)
    {
        foreach($this->newOrder as $order){

            $this->getOrder($order, $labelRepository);

        }
    }
    public function getOrder(Order $order, LabelRepository $labelRepository)
    {
        $labelData = null;

        $labelData = $labelRepository->get($order);
       
        if ( $labelData ){
            Storage::put("labels/{$order->corrios_tracking_code}.pdf", $labelData);
        }
        return redirect()->route('order.label.download',[$order,'time'=>md5(microtime())]);
      
    }
    
    public function search()
    {
        $data = $this->validate([
            'start_date' => 'nullable',
            'end_date' => 'nullable',
        ]);
        $this->start_date = $data['start_date'];
        $this->end_date   = $data['end_date'];
        $order = Order::whereBetween('arrived_date',[$this->start_date.' 00:00:00', $this->end_date.' 23:59:59'])->get();
        $this->searchOrder = $order; 
    }
    public function addOrderTracking($order)
    {
        OrderTracking::create([
            'order_id' => $order->id,
            'status_code' => Order::STATUS_ARRIVE_AT_WAREHOUSE,
            'type' => 'HD',
            'description' => 'Freight arrived at Homedelivery',
            'country' => 'US',
            'city' => 'Miami'
        ]);

        return true;
    }

    public function removeOrderTracking($tracking_code)
    {
        $order = Order::where('corrios_tracking_code', $tracking_code)->first();

        $order_tracking = OrderTracking::where('order_id', $order->id)->latest()->first();

        $order_tracking->delete();

        return true;

    }
}
