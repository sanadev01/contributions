<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Address extends Model
{
    protected $guarded = [];

    use JsonColumn;

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->dontSubmitEmptyLogs();
    }
    
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDefault(Builder $query)
    {
        return $query->where('default', true);
    }

    public function isDefault()
    {
        return $this->default;
    }

    public function isBusiness()
    {
        return $this->address_type == 'business';
    }

    public function getLastName()
    {
        $parts = explode(' ',$this->name);

        return $parts[count($parts)-1];
    }
}
