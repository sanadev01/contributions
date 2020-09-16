<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    protected $guarded = [];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
