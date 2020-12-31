<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;

class HandlingService extends Model
{
    use JsonColumn;

    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
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
