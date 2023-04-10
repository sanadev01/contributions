<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ApiLog extends Model
{
    protected $guarded = [];

    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                            ->logAll()
                            ->logOnlyDirty()
                            ->dontSubmitEmptyLogs();
    }
    
    protected $casts = [
        'data' => 'array'
    ];
    
    const TYPE_CONFIRMATION = 'confirmation';
    const TYPE_WHOLE_SALE = 'wholesale';
}
