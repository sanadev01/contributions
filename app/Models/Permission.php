<?php

namespace App\Models;

use App\Traits\TimeZoneAware;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Permission extends Model
{
    protected $guarded  =[];

    use LogsActivity;

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
