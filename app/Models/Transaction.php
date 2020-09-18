<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(PaymentInvoice::class,'invoice_id');
    }
}
