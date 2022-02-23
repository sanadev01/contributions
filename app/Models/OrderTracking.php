<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    protected $guarded = [];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function setCreatedAtAttribute()
    {
        $date = Carbon::now()->setTimezone('America/New_York');

        $this->attributes['created_at'] = $date;
    }

    public function setUpdatedAtAttribute()
    {
        $date = Carbon::now()->setTimezone('America/New_York');

        $this->attributes['updated_at'] = $date;
    }
}
