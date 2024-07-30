<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CustomResponse extends Model
{
    use LogsActivity;

    protected $fillable = [
        'batch_id', 'response',
    ];

    protected $casts = [
        'response' => 'array',
    ];
}

