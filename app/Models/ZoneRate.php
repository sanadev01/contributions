<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneRate extends Model
{
    protected $table = 'zone_rates';

    protected $fillable = [
        'shipping_service_id',
        'user_id',
        'cost_rates',
        'selling_rates',
    ];

    public function shippingService()
    {
        return $this->belongsTo(ShippingService::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
