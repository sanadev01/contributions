<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneCountry extends Model
{
    protected $table = 'zone_country';

    protected $fillable = [
        'zone_id',
        'shipping_service_id',
        'country_id',
        'profit_percentage',
    ];

    public function shippingService()
    {
        return $this->belongsTo(ShippingService::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
