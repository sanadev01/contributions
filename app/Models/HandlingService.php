<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Illuminate\Database\Eloquent\Builder;

class HandlingService extends Model
{
    use JsonColumn;

    protected $guarded = [];

    protected $casts = [
        'extra_data' => 'Object'
    ];

    public function isRateInPercent()
    {
        return $this->rate_type != 'flat';
    }

    public function scopeActive(Builder $builder)
    {
        return $builder->where('active',true);
    }
}
