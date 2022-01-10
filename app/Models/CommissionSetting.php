<?php

namespace App\Models;

use App\Models\AffiliateSale;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CommissionSetting extends Model
{
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    protected $fillable = [
        'user_id', 
        'referrer_id', 
        'type', 
        'value',
        'commission_balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function affiliateSales()
    {
        return $this->hasMany(AffiliateSale::class, 'referrer_id', 'referrer_id');
    }


}
