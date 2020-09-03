<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function hasPermissionById($permissionId)
    {
        $permissions = \Cache::remember( "roles-permissions-".$this->id, 60*60*60 , function () {
            return $this->permissions->pluck('id')->toArray();
        });

        return in_array($permissionId,$permissions);
    }
}
