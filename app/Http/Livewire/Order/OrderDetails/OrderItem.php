<?php

namespace App\Http\Livewire\Order\OrderDetails;

use App\Models\ShippingService;
use Livewire\Component;

class OrderItem extends Component
{
    public $item;
    public $keyId;
    public $order;
    public $correios;
    public $geps;
    public $prime5;
    public function mount($keyId = 0, $item = [], $order)
    {
        $this->item = $item;
        $this->keyId = $keyId;
        $this->order = $order;
        $this->geps = [
            ShippingService::GePS,
            ShippingService::GePS_EFormat,
            ShippingService::Parcel_Post,
        ];
        $this->prime5 = [
            ShippingService::Prime5RIO,
            ShippingService::Prime5
        ];
        $this->correios = [
            ShippingService::BCN_Packet_Standard,
            ShippingService::BCN_Packet_Express,
            ShippingService::Packet_Standard,
            ShippingService::Packet_Express,
            ShippingService::AJ_Packet_Standard,
            ShippingService::AJ_Packet_Express,
            ShippingService::Packet_Mini,
        ];
    }

    public function render()
    {
        return view('livewire.order.order-details.order-item');
    }
}
