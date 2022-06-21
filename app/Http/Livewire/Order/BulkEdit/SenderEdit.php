<?php

namespace App\Http\Livewire\Order\BulkEdit;

use App\Models\Order;
use Livewire\Component;

class SenderEdit extends Component
{
    public  $sender_first_name;
    public  $sender_last_name;
    public  $sender_email;
    public  $sender_phone;
    public  $sender_taxId;

    public $order;

    public function mount(Order $order)
    {
        $this->order = $order;
    }

    public function render()
    {
        $this->sender_first_name = old('first_name',__default($this->order->sender_first_name,optional($this->order->user)->name));
        $this->sender_last_name = old('last_name',__default($this->order->sender_last_name,optional($this->order->user)->last_name));
        $this->sender_email = old('email',__default($this->order->sender_email,null));
        $this->sender_phone = old('phone',__default($this->order->sender_phone,null));
        $this->sender_taxId = old('tax_id',__default($this->order->sender_taxId,null));

        return view('livewire.order.bulk-edit.sender-edit');
    }

    public function save()
    {

        $this->order->update([
            'sender_first_name' => $this->sender_first_name,
            'sender_last_name' => $this->sender_last_name,
            'sender_email' => $this->sender_email,
            'sender_phone' => $this->sender_phone,
            'sender_taxId' => $this->sender_taxId,
        ]);
    }
}
