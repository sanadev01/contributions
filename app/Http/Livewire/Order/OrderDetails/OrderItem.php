<?php

namespace App\Http\Livewire\Order\OrderDetails;

use App\Models\OrderItem as ModelsOrderItem;
use App\Models\ShCode;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use App\Rules\NcmValidator;

class OrderItem extends Component
{
    public $editItemId;
    public $sh_code;
    public $quantity;
    public $value;
    public $total;
    public $description;
    public $dangrous_item;

    public $contains_battery;
    public $contains_perfume;
    public $contains_flammable_liquid;
    public $order;
    public $correios;
    public $geps;
    public $prime5;
    public $service;

    // public $search;
    // public $name;
    public $type = 'Postal (Correios)';
    // public $orderInventory = false; 
    protected $listeners = ['loadSHCodes' => 'loadSHCodes', 'editItem' => 'editItem'];

    public function loadSHCodes($data)
    {
        $this->service = optional($data)['service'];
        $shippingService = ShippingService::where('service_sub_class', $this->service)->first();
        if (optional($shippingService)->is_total_express) {
            $this->type = 'Courier';
        } else {
            $this->type = 'Postal (Correios)';
        }
        $this->render();
        $this->dispatchBrowserEvent('initializeSelectPicker');
    }
    public function editItem($data)
    {
        if ($data) {
            $editItem = ModelsOrderItem::find($data);
            $this->editItemId = $editItem->id;
            $this->sh_code = $editItem->sh_code;
            $this->quantity = $editItem->quantity;
            $this->value = $editItem->value;
            $this->total = $this->value * $this->quantity;
            $this->description =  $editItem->description;
            $this->dangrous_item = null;
            $this->contains_battery = $editItem->contains_battery;
            if ($this->contains_battery) {
                $this->dangrous_item = 'contains_battery';
            }
            $this->contains_perfume = $editItem->contains_perfume;
            if ($this->contains_perfume) {
                $this->dangrous_item = 'contains_perfume';
            }
            $this->contains_flammable_liquid = $editItem->contains_flammable_liquid;
        }
    }
    public function resetFormFields()
    {
        // Reset the values of the specified fields
        $this->editItemId = null;
        $this->sh_code = null;
        $this->quantity = null;
        $this->value = null;
        $this->total = null;
        $this->description = null;
        $this->dangrous_item = null;
        $this->contains_flammable_liquid = null;
        $this->contains_perfume = null;
        $this->contains_battery = null;
        $this->order->refresh();
        if (count($this->order->items) == 0) {
            $this->dispatchBrowserEvent('disabledSubmitButton');
        } else {
            $this->dispatchBrowserEvent('activateSubmitButton');
        }
    }
    public function mount($order)
    {
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
    public function submitForm()
    {
        $ShCode = getValidShCode($this->sh_code);

        if($ShCode) {
            $this->sh_code = $ShCode;
        }

        $shippingService = ShippingService::where('service_sub_class', $this->service)->first();

        $rules = [
            'quantity' => 'required|numeric|min:1',
            'value' => 'required|numeric|gt:0|min:0.01',
            'sh_code' => ($this->order->products->isNotEmpty()) ? 'sometimes' : [
                'required',
                'numeric',
                new NcmValidator()
            ],
        ];

        if ($shippingService && $shippingService->is_total_express) {
            $rules['description'] = 'required|max:60';
        } else {
            $rules['description'] = 'required|max:210';
        }

        $this->validate($rules, []);

        if ($this->editItemId) {
            ModelsOrderItem::updateOrCreate(
                [
                    'id' => $this->editItemId,
                    'order_id' => $this->order->id
                ],
                [
                    'sh_code' => $this->sh_code,
                    'description' => $this->description,
                    'quantity' => $this->quantity,
                    'value' => $this->value,
                    'contains_battery' => $this->dangrous_item == 'contains_battery' ? true : false,
                    'contains_perfume' => $this->dangrous_item == 'contains_perfume' ? true : false,
                    'contains_flammable_liquid' => $this->dangrous_item == 'contains_flammable_liquid' ? true : false,
                ]
            );
            session()->flash('success', 'Item Updated Successfully.');
        } else {
            ModelsOrderItem::create([
                'order_id' => $this->order->id,
                'sh_code' => $this->sh_code,
                'description' => $this->description,
                'quantity' => $this->quantity,
                'value' => $this->value,
                'contains_battery' => $this->dangrous_item == 'contains_battery' ? true : false,
                'contains_perfume' => $this->dangrous_item == 'contains_perfume' ? true : false,
                'contains_flammable_liquid' => $this->dangrous_item == 'contains_flammable_liquid' ? true : false,
            ]);
            session()->flash('success', 'Item Added Successfully.');
        }
        $this->resetFormFields();
        $this->emitUp('itemAdded');
        $this->dispatchBrowserEvent('emitSHCodesLazy');
        $this->dispatchBrowserEvent('updateDescriptionMessage');
    }


    public function render()
    {
        ini_set('memory_limit', '10000M');
        ini_set('memory_limit', '-1');
        return view('livewire.order.order-details.order-item', [
            'codes' =>  ShCode::where('type', $this->type)->orderBy('description', 'ASC')->get(),
            'totalValue' => $this->getTotalValue(),
        ]);
    }
    // Computed property
    public function getTotalValue()
    {
        return (is_numeric($this->value) ? $this->value : 0) * (is_numeric($this->quantity) ? $this->quantity : 0);
    }
}
