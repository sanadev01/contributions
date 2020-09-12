<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentInvoice extends Model
{
    
    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
