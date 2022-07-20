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

    // public function order()
    // {
    //     return $this->hasOne(Order::class,'order_id');
    // }
}
