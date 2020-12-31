<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ApiLog extends Model
{
    protected $guarded = [];

    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    protected $casts = [
        'data' => 'Array'
    ];
    
    const TYPE_CONFIRMATION = 'confirmation';
    const TYPE_WHOLE_SALE = 'wholesale';
}
