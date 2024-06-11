<?php

namespace App\Models;

use App\Models\ShippingService;
use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Spatie\Activitylog\Traits\LogsActivity;

class ProfitPackage extends Model
{
    use JsonColumn;
    protected $guarded = [];

    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    protected $casts = [
        'data' => 'Array'
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
