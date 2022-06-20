<?php

namespace App\Http\Livewire\Order\OrderDetails;

use Livewire\Component;

class OrderItem extends Component
{
    public $item;
    public $keyId;
    public $order;

    public function mount($keyId=0,$item=[],$order)
    {
        $this->item = $item;
        $this->keyId = $keyId;
        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.order.order-details.order-item');
    }
}
