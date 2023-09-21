<?php

namespace App\Models;

use App\Models\Commune;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $guarded = [];

    public function communes()
    {
        return $this->hasMany(Commune::class);
    }

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }
}
