<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class HandlingService extends Model
{
    use JsonColumn;
    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                            ->logAll()
                            ->logOnlyDirty()
                            ->dontSubmitEmptyLogs();
    }
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
