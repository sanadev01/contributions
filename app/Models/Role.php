<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Role extends Model
{
    protected $guarded = [];
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

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
