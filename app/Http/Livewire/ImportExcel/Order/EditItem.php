<?php

namespace App\Http\Livewire\ImportExcel\Order;

use App\Models\ImportedOrder;
use App\Models\ShCode;
use App\Models\ShippingService;
use Livewire\Component;

class EditItem extends Component
{
    public $orderId; 
    public $order; 
    public $items; 
    public $shCodes = [];
    public $shippingServices; 
    public $customer_reference;
    public $user_declared_freight;
    public $shipping_service_id;

    public function mount($order)
    {
        $this->order = $order;   
        $this->items = $this->order->items;   
        $this->shCodes = ShCode::all()->toArray();
        
    }

    public function render()
    {
        return view('livewire.import-excel.order.edit-item');
    }

    public function save()
    {
        if ( !$this->validateItems() ){
            return;
        }

        $this->order->update([
            'items' => $this->items,
        ]);
    }
    
    // public function getShippingServices()
    // {
    //     // $shippingServices = collect() ;
    //     // foreach (ShippingService::query()->active()->get() as $shippingService) {
    //     //     if ( $shippingService->isAvailableFor($this->order) ){
    //     //         $shippingServices->push($shippingService);
    //     //     }else{
    //     //         session()->flash('alert-danger',"Shipping Service not Available Error:{$shippingService->getCalculator($this->order)->getErrors()}");
    //     //     }
    //     // }
    //     // return $shippingServices;
    // }

    function validateItems()
    {
        $errors = false;
        $this->resetValidation();
        foreach ($this->items as $index=>$item) {
            if ( !is_numeric($item['sh_code']) ){
                $this->addError("items.{$index}.sh_code","Invalid Sh Code");
                $errors= true;
            }
            if ( strlen($item['description'])<=0 || strlen($item['description'])>190 ){
                $this->addError("items.{$index}.description","Description length must be between 1-190 characters");
                $errors= true;
            }
            if ( !is_numeric($item['quantity']) ){
                $this->addError("items.{$index}.quantity","Invalid Quantity");
                $this->items[$index]['quantity'] = 0;
                $errors= true;
            }
            if ( !is_numeric($item['value']) ){
                $this->addError("items.{$index}.value","Invalid Value");
                $this->items[$index]['value'] = 0;
                $errors= true;
            }
        }

        return !$errors;
    }
}
