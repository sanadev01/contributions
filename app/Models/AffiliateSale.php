<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateSale extends Model
{
    protected $fillable = [
        'user_id', 
        'order_id', 
        'value', 
        'type',
        'commission',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class,'user_id');
    }
}
