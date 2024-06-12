<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    //
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }
    public function getIsRefundedAttribute()
    {
        return optional($this->deposit)->last_four_digits == 'Tax refunded';
    }
    public function getIsTaxAttribute()
    {
        return !$this->is_refunded && !$this->is_adjustment;
    }
    public function getIsAdjustmentAttribute()
    {
        return  $this->adjustment != null;
    }

}
