<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class ProfitSetting extends Model
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
