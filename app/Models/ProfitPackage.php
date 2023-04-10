<?php

namespace App\Models;

use App\Models\ShippingService;
use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class ProfitPackage extends Model
{
    use JsonColumn;
    protected $guarded = [];

    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                            ->logAll()
                            ->logOnlyDirty()
                            ->dontSubmitEmptyLogs();
    }
    
    protected $casts = [
        'data' => 'array'
    ];

    const SERVICE_BPS = 'bps';
    const SERVICE_PACKAGE_PLUS = 'package-plus';
    const SERVICE_PACKAGE_PROFIT = 'package-profit';

    const TYPE_DEFAULT = 'default';
    const TYPE_PACKAGE = 'package';

    public function shippingService()
    {
        return $this->belongsTo(ShippingService::class);
    }

}
