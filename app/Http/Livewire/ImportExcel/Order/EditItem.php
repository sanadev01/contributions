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
    public $edit; 
    public $items; 
    public $shCodes = [];
    public $customer_reference;
    public $user_declared_freight;

    public function mount($order, $edit= '')
    {
        $this->edit = $edit;   
        $this->order = $order;   
        $this->customer_reference = $order->customer_reference;   
        $this->user_declared_freight = $order->user_declared_freight;   
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
        

        $data = $this->validate([
            'customer_reference' => 'nullable',
            'user_declared_freight' => 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
        ]);

        $error = $this->order->error;
        if($error){
            $remainError = array_diff($error, $this->messages());
            $error = $remainError ? $remainError : null;
        }

        $this->order->update([
            'customer_reference' => $data['customer_reference'],
            'user_declared_freight' => $data['user_declared_freight'],
            'items' => $this->items,
            'error' => $error,
        ]);

        if(!$error){
            return redirect()->route('admin.import.import-excel.show', $this->order->import_id);
        }

    }

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
    
    public function messages()
    {
        return [
            'quantity.required' => 'quantity is required',
            'value.required' => 'value is required',
            'description.required' => 'Product name description required',
            'sh_code.required' => 'NCM sh code is required',
        ];
    }
}
