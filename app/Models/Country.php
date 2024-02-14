<?php

namespace App\Models;

use App\Models\Region;
use App\Models\Commune;
use App\Models\Warehouse\AccrualRate;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Country extends Model
{
    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                            ->logAll()
                            ->logOnlyDirty()
                            ->dontSubmitEmptyLogs();
    }
    
    protected $fillable = [
        'name', 'code'
    ];

    const Chile = 46;
    const Brazil = 30;
    const US = 250;
    const Portugal = 188;
    const Colombia = 50;
    const Japan = 114;
    const UK = 249;

    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function communes()
    {
        return $this->hasManyThrough(Commune::class, Region::class);
    }

    public function accrualRates()
    {
        return $this->hasMany(AccrualRate::class);
    }

    public function zoneCountries()
    {
        return $this->hasMany(ZoneCountry::class);
    }
}
