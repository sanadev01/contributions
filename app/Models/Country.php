<?php

namespace App\Models;

use App\Models\Warehouse\AccrualRate;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Country extends Model
{
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    protected $fillable = [
        'name', 'code'
    ];

    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function accrualRates()
    {
        return $this->hasMany(AccrualRate::class);
    }
}
