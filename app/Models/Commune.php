<?php

namespace App\Models;

use App\Models\Region;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $guarded = [];


    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
