<?php

namespace App\Models;

use App\Models\Commune;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $guarded = [];

    const COLOMBIA_SENDER_CODE = '11001000';

    public function communes()
    {
        return $this->hasMany(Commune::class);
    }
}
