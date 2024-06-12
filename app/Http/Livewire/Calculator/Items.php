<?php

namespace App\Http\Livewire\Calculator;

use Livewire\Component;

class Items extends Component
{
    public $items = [];

    public function render()
    {
        if ( count($this->items) <1 ){
            $this->addItem();
        }

        return view('livewire.calculator.items');
    }

    public function addItem()
    {
        array_push($this->items,[]);
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
    }

}
