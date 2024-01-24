<?php

namespace App\Http\Livewire\Order\OrderDetails;

use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Component;

class OrderItems extends Component
{
    public $orderId;
    public $order;
    public $editItemId = null;
    protected $listeners = ['itemAdded'];



    public function itemAdded()
    {
        $this->order->refresh();
    }
    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->order = Order::find($orderId);
        
 
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
