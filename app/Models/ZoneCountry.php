<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneCountry extends Model
{
    protected $table = 'zone_country';

    protected $fillable = [
        'zone_id',
        'country_id',
        'profit_percentage',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
