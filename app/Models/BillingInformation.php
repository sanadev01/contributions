<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class BillingInformation extends Model
{
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'card_no', 'expiration', 'cvv', 'phone', 'address', 'state', 'zipcode', 'country'
    ];

}
