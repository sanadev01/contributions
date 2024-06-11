<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class OrderService extends Model
{
    protected $guarded = [];

    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
