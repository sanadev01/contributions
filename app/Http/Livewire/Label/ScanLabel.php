<?php

namespace App\Http\Livewire\Label;

use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\Order;
use Livewire\Component;
use App\Mail\User\Shipment;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Services\GePS\Client;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Repositories\LabelRepository;
use Illuminate\Support\Facades\Storage;

class ScanLabel extends Component
{
    public $packagesRows;
    public $tracking = '9261290221581600001287';
    public $orderStatus = '';
    public $start_date;
    public $end_date;
    public $user_id;
    public $error = '';
    public $order = [];
    public $searchOrder = [];
    public $newOrder = [];
    public $totalWeight = 0;
    public $totalPieces = 0;
    public $excel = 0;

    protected $listeners = ['user:updated' => 'getUser',
                             'clear-search' => 'removeUser'   
                            ];

    public $ids=[];
    public $refs=[];
    
    // protected $rules = [
    //     'ids' => 'required|array',
    //     'refs' => 'required|array',
    // ]; 
    
    public function submit()
    {
        dd($this->ids);
        foreach($this->ids as $key=>$id){ 
            $order = Order::find($id); 
            $order->update([
                'customer_reference'=>$this->refs[$key],
            ]);
            $this->packagesRows[$key]['customer_reference'] = $this->refs[$key];
        }
        // $this->render();
    }

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
            'us_api_tracking_code' => '',
            'client' => '',
            'dimensions' => '',
            'customer_reference' => '',
            'kg' => '',
            'reference' => '',
            'recpient' => '',
            'order_date' => '',
            'tracking_id' => '',
        ]);
    }

    public function removeRow($index)
    {
        unset($this->ids[$index]);
        unset($this->refs[$index]);
        $this->removeOrderTracking($this->packagesRows[$index]['id']);
        unset($this->packagesRows[$index]);
    }
    
    public function getTrackingCode(string $trackingCode, $index)
    {
        $order = Order::where('corrios_tracking_code', $trackingCode)->first();
        $this->order = $order;

        if(!$order->isPaid() && !$order->is_paid){ 
            $this->dispatchBrowserEvent('get-error', ['errorMessage' => 'Order Payment is pending']);
            return $this->tracking = '';
        }
        if($this->order){
            $this->packagesRows[$index]['tracking_code'] = $trackingCode;
            $this->packagesRows[$index]['us_api_tracking_code'] = $this->order->us_api_tracking_code;
            $this->packagesRows[$index]['pobox'] = $this->order->user->pobox_number;
            $this->packagesRows[$index]['driver'] =  optional(optional($this->order->driverTracking)->user)->name;
            $this->packagesRows[$index]['pickup_date'] = optional(optional($this->order->driverTracking)->created_at)->format('m-d-Y');
            $this->packagesRows[$index]['client'] = $this->order->merchant;
            $this->packagesRows[$index]['dimensions'] = $this->order->length . ' x ' . $this->order->length . ' x ' . $this->order->height ;
            $this->packagesRows[$index]['kg'] = $this->order->getWeight('kg');
            $this->packagesRows[$index]['reference'] = $this->order->id;
            $this->packagesRows[$index]['tracking_id'] = $this->order->tracking_id;
            $this->packagesRows[$index]['recpient'] = $this->order->recipient->first_name;
            $this->packagesRows[$index]['order_date'] = $this->order->order_date->format('m-d-Y');
            
            $this->addRow();
        }
    }
    
    public function updatedTracking()
    {
        if($this->tracking){
            
            $orders = Order::where('corrios_tracking_code', $this->tracking)->orwhere('us_api_tracking_code',$this->tracking)->get();
            $existCount = 0;
            $date = (new DateTime('America/New_York'))->format('Y-m-d h:i:s');
            
            foreach($orders as $order){
                
            if($order){
                if($order->trackings->isNotEmpty() && $order->trackings()->latest()->first()->status_code >= Order::STATUS_ARRIVE_AT_WAREHOUSE){
                    $lastScanned = $order->trackings()->where('status_code',Order::STATUS_ARRIVE_AT_WAREHOUSE)->first();
                    
                    if ($lastScanned) {
                        $this->dispatchBrowserEvent('get-error', ['errorMessage' => 'package already scanned on '.$lastScanned->created_at->format('m/d/Y H:i:s').'']);
                    }
                }
                if($order->status == Order::STATUS_REFUND){
                    $this->dispatchBrowserEvent('get-error', ['errorMessage' => 'Order is Cancelled / Refunded']);
                    return $this->tracking = '';
                }
                if(!$order->is_paid){
                    $this->dispatchBrowserEvent('get-error', ['errorMessage' => 'Order Payment is pending']);
                    return $this->tracking = '';
                }
                if($order->status == Order::STATUS_CANCEL){
                    $this->dispatchBrowserEvent('get-error', ['errorMessage' => 'Order is Cancelled']);
                    return $this->tracking = '';
                }
                
                if($order->status == Order::STATUS_REJECTED){
                    $this->dispatchBrowserEvent('get-error', ['errorMessage' => 'Order Rejected']);
                    return $this->tracking = '';
                }
                
                if($order->status == Order::STATUS_RELEASE){
                    $this->dispatchBrowserEvent('get-error', ['errorMessage' => 'Order Release']);
                    return $this->tracking = '';
                } 
                $newRow = [
                    'id' => $order->id,
                    'tracking_code' => $order->corrios_tracking_code,
                    'us_api_tracking_code' => $order->us_api_tracking_code,
                    'pobox' => $order->user->pobox_number,
                    'driver' => optional(optional($order->driverTracking)->user)->name,
                    'pickup_date' => optional(optional($order->driverTracking)->created_at)->format('m-d-Y'),
                    'client' => $order->merchant,
                    'dimensions' => $order->length . ' x ' . $order->length . ' x ' . $order->height,
                    'kg' => $order->getWeight('kg'),
                    'reference' => $order->id,
                    'customer_reference' => $order->customer_reference,
                    'tracking_id' => $order->tracking_id,
                    'recpient' => $order->recipient->first_name,
                    'order_date' => $order->order_date->format('m-d-Y'),
                ];

                if($this->orderExist($order->id, $this->packagesRows)){
                    $existCount++;
                     continue;
                }
                
                array_push($this->packagesRows, $newRow);

                array_push($this->newOrder,$order);

                array_push($this->ids,$order->id);
                array_push($this->refs,$order->customer_reference);
      
                if(auth()->user()->isScanner() && $order->trackings->isNotEmpty() && $order->trackings()->latest()->first()->status_code >= Order::STATUS_PAYMENT_DONE && $order->trackings()->latest()->first()->status_code < Order::STATUS_ARRIVE_AT_WAREHOUSE)
                {
                    $this->addOrderTracking($order);
                    if(!$order->arrived_date){
                        $order->update([
                            'arrived_date' => $date
                        ]);
                    }
                }
                
            }
            }
            if($existCount>0)
            $this->dispatchBrowserEvent('get-error', ['errorMessage' => $existCount==1||count($orders)==$existCount?'Order already exist':$existCount." orders already exist ".(count($orders)-$existCount)." order Checked In"]);

                $this->tracking = '';
        }
        $this->tracking = '';
    }
    function orderExist($id, $orders) {
        foreach ($orders as   $order) {
            if ($order['id'] === $id) {
                return true;
            }
        }
        return false;
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
        return redirect()->route('order.label.download',[ encrypt($order->id) ,'time'=>md5(microtime())]);
      
    }
    
    public function search()
    {
        $data = $this->validate([
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'user_id' => 'nullable',
        ]);

        $this->start_date = $data['start_date'];
        $this->end_date   = $data['end_date'];
        $this->user_id    = $data['user_id'];

        if($this->user_id != null)
        {
            $order = Order::where('user_id', $this->user_id)->whereBetween('arrived_date',[$this->start_date.' 00:00:00', $this->end_date.' 23:59:59'])->orderBy('arrived_date', 'DESC')->get();
            $this->searchOrder = $order;
            $this->totalPieces = $this->searchOrder->count();
            $this->calculateTotalWeight();
            
            return true;
        }

        $order = Order::whereBetween('arrived_date',[$this->start_date.' 00:00:00', $this->end_date.' 23:59:59'])->orderBy('arrived_date', 'DESC')->get();
        $this->searchOrder = $order;
        $this->totalPieces = $this->searchOrder->count();
        $this->calculateTotalWeight();
    }

    public function getUser($userId)
    {
        $this->user_id = $userId;
    }

    public function removeUser()
    {
        $this->user_id = null;
    }

    public function calculateTotalWeight()
    {
        if($this->searchOrder)
        {
            $this->totalWeight = 0;
            foreach($this->searchOrder as $order)
            {
                $this->totalWeight += $order->getWeight('kg');
            }
        }
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

    public function removeOrderTracking($id)
    {
        $order = Order::where('id', $id)->first();
        
        if($order->trackings->isNotEmpty() && $order->trackings()->count() < 3)
        {
            $order->trackings()->latest()->first()->delete();
        }

        return true;

    }
}
