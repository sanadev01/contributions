<?php

namespace App\Http\Livewire\ImportExcel\Order;

use App\Models\ImportedOrder;
use Livewire\Component;

class EditSender extends Component
{
    public $order; 

    public $sender_first_name;
    public $sender_last_name;
    public $sender_email;
    public $sender_phone;
    public $sender_taxId;

    public function mount($order)
    {
        $this->order = $order;
        $this->sender_first_name = old('sender_first_name',  $this->order->sender_first_name );
        $this->sender_last_name = old('sender_last_name', $this->order->sender_last_name);
        $this->sender_email = old('sender_email', $this->order->sender_email);
        $this->sender_phone = old('sender_phone', $this->order->sender_phone);
    }

    public function render()
    {
        return view('livewire.import-excel.order.edit-sender');
    }

    public function save()
    {
        $data = $this->validate([
            'sender_first_name' => 'required|max:100',
            'sender_last_name' => 'max:100',
            'sender_email' => 'nullable|max:100|email',
            'sender_phone' => 'nullable|max:15'
        ]);

        $this->order->update($data);
    }
}
