<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProfitSetting extends Model
{
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    protected $fillable = [
        'user_id','package_id','service_id'
    ];
    
    public function profitPackage()
    {
        return $this->belongsTo(ProfitPackage::class, 'package_id');
    }
    
    public function shippingService()
    {
        return $this->belongsTo(ShippingService::class, 'service_id');
    }
}
