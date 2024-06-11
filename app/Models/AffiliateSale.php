<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateSale extends Model
{
    use SoftDeletes;

    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    protected $fillable = [
        'user_id', 
        'order_id', 
        'value', 
        'type',
        'commission',
        'detail',
        'referrer_id'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function referrer()
    {
        return $this->belongsTo(User::class,'referrer_id');
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }

    public function commissionSetting()
    {
        return $this->belongsTo(CommissionSetting::class, 'referrer_id', 'referrer_id');
    }
}
