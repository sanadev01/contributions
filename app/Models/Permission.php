<?php

namespace App\Models;

use App\Traits\TimeZoneAware;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Permission extends Model
{
    protected $guarded  =[];

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                            ->logAll()
                            ->logOnlyDirty()
                            ->dontSubmitEmptyLogs();
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
