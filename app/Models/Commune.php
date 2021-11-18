<?php

namespace App\Models;

use App\Models\Region;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $guarded = [];


    public function regions()
    {
        return $this->belongsTo(Region::class);
    }
}
