<?php

namespace App\Http\Livewire\Tax;

use Livewire\Component;
use App\Models\Order;


class Tax extends Component
{
    public $trackingNumbers;
    public $user_id;

    public function render()
    {
        //dd($this->user_id);
        $orders = null;
        //dd($orders);
        return view('livewire.tax.tax', [
            'orders' => $orders,
        ]);
    }

    public function search()
    {
        $data = $this->validate([
            'user_id' => 'required',
            'trackingNumbers' => 'required',
        ]);

        dd($data);

        // $trackingNumber = explode(',', preg_replace('/\s+/', '', $this->trackingNumbers));
        //     $orders = Order::whereIn('corrios_tracking_code', $trackingNumber)->get();
    }

}
