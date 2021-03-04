<?php

namespace App\Http\Livewire\Label;

use App\Models\Order;
use Livewire\Component;
use Illuminate\Http\Request;
use App\Repositories\LabelRepository;
use Illuminate\Support\Facades\Storage;

class ScanLabel extends Component
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
        ]);
    }

    public function removeRow($index)
    {
        unset($this->packagesRows[$index]);
    }
    
    public function getTrackingCode(string $trackingCode, $index)
    {
        $order = Order::where('corrios_tracking_code', $trackingCode)->first();
        $this->order = $order;
        
        if($this->order){
            $this->packagesRows[$index]['tracking_code'] = $trackingCode;
            $this->packagesRows[$index]['client'] = $this->order->merchant;
            $this->packagesRows[$index]['dimensions'] = $this->order->length . ' x ' . $this->order->length . ' x ' . $this->order->height ;
            $this->packagesRows[$index]['kg'] = $this->order->weight;
            $this->packagesRows[$index]['reference'] = $this->order->id;
            $this->packagesRows[$index]['recpient'] = $this->order->recipient->first_name;
            
            $this->addRow();
        }
    }
    
    public function updatedTracking()
    {
        $order = Order::where('corrios_tracking_code', $this->tracking)->first();
        $this->order = $order;
        $this->orderStatus = '';

        if($this->order->status == Order::STATUS_CANCEL){
            $this->orderStatus = 'Order Cancel';
            return $this->tracking = '';
        }
        
        if($this->order){
            
            array_push($this->packagesRows,[
                'tracking_code' => $this->tracking,
                'client' => $this->order->merchant,
                'dimensions' => $this->order->length . ' x ' . $this->order->length . ' x ' . $this->order->height,
                'kg' => $this->order->weight,
                'reference' => $this->order->id,
                'recpient' => $this->order->recipient->first_name,
            ]);
            
            array_push($this->newOrder,$this->order);
        }
            
        $this->tracking = '';
    }

    public function printLabel(LabelRepository $labelRepository)
    {
        foreach($this->newOrder as $order){

            // dd($order);
            $this->getOrder($order, $labelRepository);
            
        }
    }
    public function getOrder(Order $order, LabelRepository $labelRepository)
    {
    //    dd($order);
        $labelData = null;

        // if ( $request->update_label === 'true' ){
        //     $labelData = $labelRepository->update($order);
        // }else{
            $labelData = $labelRepository->get($order);
        // }

        // $order->refresh();

        if ( $labelData ){
            Storage::put("labels/{$order->corrios_tracking_code}.pdf", $labelData);
        }
        return redirect()->route('order.label.download',[$order,'time'=>md5(microtime())]);
      
    }
}
