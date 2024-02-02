<?php

namespace App\Http\Livewire\Order\OrderDetails;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShCode;
use App\Models\ShippingService;
use Livewire\Component;

class OrderItems extends Component
{
    public $orderId;
    public $order;
    public $shippingService;
    public $editItemId = null;
    protected $listeners = ['loadSHCodes' => 'loadSHCodes', 'itemAdded' => 'itemAdded'];

    public function loadSHCodes($data)
    {
        $service = optional($data)['service'];
        $this->shippingService = ShippingService::where('service_sub_class', $service)->first();        
        $this->render(); 
    }


    public function itemAdded()
    {
        $this->order->refresh();
    }
    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->order = Order::find($orderId);
        $this->shippingService = $this->shippingService??$this->order->shippingService;
    }
    public function isValidShCode($shCode){
        $this->shippingService = $this->shippingService??$this->order->shippingService;
        $itemType = $this->shippingService->is_total_express ? 'total' : null;
        return ShCode::where('code', $shCode)->where('type', $itemType)->first() == null;
    }

    public function render()
    {
        return view('livewire.order.order-details.order-items');
    }



    public function deleteItem($id)
    {
        OrderItem::where('order_id', $this->order->id)->where('id', $id)->delete();
        $this->order->refresh();
        if(count($this->order->items)==0)
        {
            $this->dispatchBrowserEvent('disabledSubmitButton');
        }
        else{
            $this->dispatchBrowserEvent('activateSubmitButton');
        }
        $this->dispatchBrowserEvent('emitSHCodes');
    }


    public function editItem($id)
    {
        $this->emit('editItem', $id);
        $this->dispatchBrowserEvent('emitSHCodes');
    }
}
