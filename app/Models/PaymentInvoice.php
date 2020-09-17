<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentInvoice extends Model
{
    protected $guarded = [];

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'paid_by');
    }


    public function isPaid()
    {
        return $this->is_paid;
    }

    public static function generateUUID()
    {
        return uniqid('PI-');
    }
}
