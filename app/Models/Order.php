<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];
    
    const STATUS_PREALERT_TRANSIT = 10;
    const STATUS_PREALERT_READY = 20;
    const STATUS_ORDER = 30;
    const STATUS_CONSOLIDATOIN_REQUEST = 40;
    const STATUS_CONSOLIDATED = 50;
    const STATUS_PAYMENT_PENDING = 60;
    const STATUS_PAYMENT_DONE = 70;
    const STATUS_SHIPPED = 80;
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isShipmentAdded()
    {
        return $this->is_shipment_added;
    }
    
    public function isPaid()
    {
        return $this->is_paid;
    }


}
