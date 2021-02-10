<?php

namespace App\Http\Livewire\Order\OrderDetails;

use Livewire\Component;

class OrderItem extends Component
{
    public $item;
    public $keyId;

    public function mount($keyId=0,$item=[])
    {
        $this->item = $item;
        $this->keyId = $keyId;
    }

    public function render()
    {
        return view('livewire.order.order-details.order-item');
    }
}
