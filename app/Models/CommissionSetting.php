<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    protected $fillable = [
        'user_id', 
        'type', 
        'value',
        'commission_balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
