<?php

namespace App\Observers;

use App\Models\Order;

class CorreiosOrderObserver
{
    public function updated(Order $order)
    {
       dd($order);
    }
}
