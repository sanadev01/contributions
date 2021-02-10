<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Services\Calculators\RatesCalculator;

class ShippingService extends Model
{
    use JsonColumn;
    use LogsActivity;

    protected $guarded = [];
    
    protected static $logAttributes = ['*'];
    
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public $cacheCalculator = false;


    protected static $calculator;

    public function rates()
    {
        return $this->hasMany(Rate::class,'shipping_service_id');
    }

    public function scopeActive(Builder $builder)
    {
        return $builder->where('active',true);
    }

    public function isAvailableFor(Order $order)
    {
        return $this->getCalculator($order)->isAvailable();
    }

    public function getRateFor(Order $order,$withProfit=true, $calculateOnVolumeMetricWeight = true)
    {
        $rate = round($this->getCalculator($order, $calculateOnVolumeMetricWeight)->getRate($withProfit),2);
        return $rate;
    }

    public function getCalculator(Order $order, $calculateOnVolumeMetricWeight = true)
    {
        // if ( self::$calculator && $this->cacheCalculator) 
        //     return self::$calculator;
        
        self::$calculator = new RatesCalculator($order,$this, $calculateOnVolumeMetricWeight);

        return self::$calculator;
    }
}
