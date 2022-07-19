<?php

namespace App\Http\Livewire\Tax;

use Livewire\Component;
use App\Models\Order;


class Tax extends Component
{
    public $user_id;
    public $trackings;
    public $order;
    public $orders = [];


    public function render()
    {
        return view('livewire.tax.tax');
    }
    public function sreach()
    {
        dd($this->trackings);

        if($this->trackings) {

            foreach($trackings as $tracking) {

                $order = Order::where('corrios_tracking_code', $this->tracking)->orWhere('id', $this->tracking)
                ->orWhere('warehouse_number', $this->tracking)->first();
                $this->order = $order;

                array_push($this->orders,[
                    'tracking_code' => $this->tracking,
                    'client' => $this->order->merchant,
                    'reference' => $this->order->warehouse_number,
                    'recpient' => $this->order->recipient->first_name.' '.$this->order->recipient->last_name,
                    'kg' => $this->order->getWeight('kg'),
                    'status' => $this->order->status
                ]);
            }

        }

        $this->trackings = '';


    }
}
