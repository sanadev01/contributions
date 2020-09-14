<?php

namespace App\Models;

use App\Services\Calculators\RatesCalculator;
use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Illuminate\Database\Eloquent\Builder;

class ShippingService extends Model
{
    use JsonColumn;

    protected $guarded = [];

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

    public function getRateFor(Order $order,$withProfit=true)
    {
        return $this->getCalculator($order)->getRate($withProfit);
    }

    public function getCalculator(Order $order)
    {
        if ( self::$calculator ) 
            return self::$calculator;

        self::$calculator = new RatesCalculator($order,$this);

        return self::$calculator;
    }
}
