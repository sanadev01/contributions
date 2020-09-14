<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;

class Rate extends Model
{
    use JsonColumn;

    protected $guarded = [];

    protected $casts = [
        'data' => 'Array',
    ];


    public function scopeByCountry(Builder $builder,$countryId)
    {
        return $builder->where('country_id',$countryId);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
