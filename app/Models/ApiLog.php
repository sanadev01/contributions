<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'data' => 'Array'
    ];
    
    const TYPE_CONFIRMATION = 'confirmation';
    const TYPE_WHOLE_SALE = 'wholesale';
}
