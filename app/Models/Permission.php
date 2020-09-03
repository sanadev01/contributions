<?php

namespace App\Models;

use App\Traits\TimeZoneAware;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guarded  =[];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
