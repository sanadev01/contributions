<?php

namespace App\Models;

use App\Models\Commune;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $guarded = [];

    const COLOMBIA_SENDER_CODE = '11001000';
    const REGION_SANTIAGO = '214';

    public function communes()
    {
        return $this->hasMany(Commune::class);
    }

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }
}
