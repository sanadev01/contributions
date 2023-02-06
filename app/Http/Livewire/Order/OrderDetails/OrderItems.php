<?php

namespace App\Http\Livewire\Order\OrderDetails;

use App\Models\Order;
use Livewire\Component;

class OrderItems extends Component
{
    public $orderId; 
    public $items;
    public $order;

    protected $listeners = [
        'removeItem' => 'removeItem'
    ];

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->order = Order::find($orderId);
        
        $this->items = old('items', $this->order->items->toArray() );

        if ( count($this->items) <1 ){
            $this->addItem();
            // $this->addItem();
        }

    }

    public function render()
    {
        return view('livewire.order.order-details.order-items');
    }

    public function addItem()
    {
        array_push($this->items,[]);
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
    }
}
