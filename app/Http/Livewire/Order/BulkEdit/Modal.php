<?php

namespace App\Http\Livewire\Order\BulkEdit;

use App\Models\Order;
use Livewire\Component;

class Modal extends Component
{
    protected $listeners = [
        'edit-order' => 'editOrder',
        'close-order-edit' => 'close',

    ];

    public $shown = false;
    private $order;

    public function mount()
    {
        $this->shown = false;
    }

    public function render()
    {
        return view('livewire.order.bulk-edit.modal',[
            'order' => $this->order
        ]);
    }

    public function editOrder($orderId)
    {
        $this->order = Order::find($orderId);
        $this->shown= true;
    }

    public function close()
    {
        $this->shown = false;
    }
}
