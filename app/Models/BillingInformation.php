<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BillingInformation extends Model
{
    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                            ->logAll()
                            ->logOnlyDirty()
                            ->dontSubmitEmptyLogs();
    }
    
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'card_no', 'expiration', 'cvv', 'phone', 'address', 'state', 'zipcode', 'country'
    ];

}
