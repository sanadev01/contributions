<?php

namespace App\Http\Livewire\ImportExcel\Order;
use App\Models\ImportedOrder;
use Livewire\Component;

class Modal extends Component
{
    protected $listeners = [
        'edit-order' => 'editOrder',
        'close-order-edit' => 'close',
    ];
    
    public $shown = false;
    private $order;
    
    public function render()
    {
        return view('livewire.import-excel.order.modal',[
            'order' => $this->order
        ]);
    }

    public function editOrder($orderId)
    {
        $this->order = ImportedOrder::find($orderId);
        $this->shown= true;
    }

    public function close()
    {
        $this->shown = false;
    }
}
