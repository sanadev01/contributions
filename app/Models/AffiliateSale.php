<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateSale extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id', 
        'order_id', 
        'value', 
        'type',
        'commission',
        'detail',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
}
