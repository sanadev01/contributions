<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZoneCountry extends Model
{
    protected $table = 'zone_country';

    protected $fillable = [
        'zone_id',
        'country_id',
        'profit_percentage',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
