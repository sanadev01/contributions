<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['name'];

    public function zoneCountries()
    {
        return $this->hasMany(ZoneCountry::class);
    }
}
