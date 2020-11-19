<?php

namespace App\Http\Livewire\Order\BulkEdit;

use App\Models\OrderItem;
use App\Models\ShCode;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class ItemsEdit extends Component
{
    public $items;
    public $shCodes = [];

    public function mount(Collection $items)
    {
        $this->items = $items->toArray();
        $this->shCodes = ShCode::all()->toArray();
    }

    public function render()
    {
        return view('livewire.order.bulk-edit.items-edit');
    }

    public function save()
    {
        if ( !$this->validateItems() ){
            return;
        }

        foreach( $this->items as $item ){
            $id = $item['id']; unset($item['id']);
            OrderItem::find($id)->update($item);
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

}
