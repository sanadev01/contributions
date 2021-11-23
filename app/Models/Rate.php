<?php

namespace App\Models;

use App\Models\Region;
use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;

class Rate extends Model
{
    use JsonColumn;
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected $guarded = [];

    protected $casts = [
        'data' => 'Array',
    ];


    public function shippingService()
    {
        return $this->belongsTo(ShippingService::class);
    }

    public function scopeByCountry(Builder $builder,$countryId)
    {
        return $builder->where('country_id',$countryId);
    }

    public function scopeByRegion(Builder $builder,$regionId)
    {
        return $builder->where('region_id', $regionId);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
