<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;

class OrderItem extends Model
{
    protected $guarded = [];
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
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
