<?php

namespace App\Models;

use App\Traits\TimeZoneAware;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guarded  =[];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
