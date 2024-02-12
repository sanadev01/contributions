<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Role extends Model
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

    const Driver = 'driver';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission($permissionId)
    {
        return $this->permissions()->where('role_id',$this->id)->where('permission_id',$permissionId)->first();
    }
}
