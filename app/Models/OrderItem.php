<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeBatteries(Builder $builder)
    {
        return $builder->where('contains_battery',true);
    }

    public function scopePerfumes(Builder $builder)
    {
        return $builder->where('contains_perfume',true);
    }
}
