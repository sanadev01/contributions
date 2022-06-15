<?php

namespace App\Models;

use App\Models\ProfitPackage;
use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Services\Calculators\RatesCalculator;

class ShippingService extends Model
{
    use JsonColumn;
    use LogsActivity;

    const API_CORREIOS = 'api_correios';
    const API_LEVE = 'api_leve';

    const USPS_PRIORITY = 3440;
    const USPS_FIRSTCLASS = 3441;
    const USPS_PRIORITY_INTERNATIONAL = 3442;
    const USPS_FIRSTCLASS_INTERNATIONAL = 3443;
    const SRP = 28;
    const SRM = 32;
    const Courier_Express = 33;
    const UPS_GROUND = 03;
    const FEDEX_GROUND = 04;
    const Packet_Standard = 33162;
    const Packet_Express = 33170;
    const Packet_Mini = 33197;
    const AJ_Packet_Standard = 33164;
    const AJ_Packet_Express = 33172;
    const COLOMBIA_Standard = 44162;

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

    public function profitPackages()
    {
        return $this->hasMany(ProfitPackage::class);
    }

    public function isOfUnitedStates()
    {
        if (collect($this->usShippingServices())->contains($this->service_sub_class)) {
            return true;
        }

        return false;
    }

    public function isDomesticService()
    {
        if (collect($this->domesticShippingServices())->contains($this->service_sub_class)) {
            return true;
        }
        
        return false;
    }

    public function isInternationalService()
    {
        if (collect($this->internationalShippingServices())->contains($this->service_sub_class)) {
            return true;
        }

        return false;
    }

    public function isCorreiosService()
    {
        if (collect($this->correiosShippingServices())->contains($this->service_sub_class)) {
            return true;
        }
    
        return false;
    }

    public function isAnjunService()
    {
        if (collect($this->anjunShippingServices())->contains($this->service_sub_class)) {
            return true;
        }

        return false;
    }

    public function isColombiaService()
    {
        if (collect($this->colombiaShippingServices())->contains($this->service_sub_class)) {
            return true;
        }

        return false;
    }

    public function isUSPSService()
    {
        if (collect($this->uspsShippingServices())->contains($this->service_sub_class)) {
            return true;
        }

        return false;
    }

    private function anjunShippingServices()
    {
        return [
            self::AJ_Packet_Standard, 
            self::AJ_Packet_Express,
        ];
    }

    private function correiosShippingServices()
    {
        return [
            self::Packet_Standard, 
            self::Packet_Express,
            self::Packet_Mini,
        ];
    }

    private function usShippingServices()
    {
        return [
            self::USPS_PRIORITY, 
            self::USPS_FIRSTCLASS, 
            self::USPS_PRIORITY_INTERNATIONAL, 
            self::USPS_FIRSTCLASS_INTERNATIONAL, 
            self::UPS_GROUND, 
            self::FEDEX_GROUND
        ];
    }

    private function domesticShippingServices()
    {
        return [
            self::USPS_PRIORITY, 
            self::USPS_FIRSTCLASS,
            self::UPS_GROUND, 
            self::FEDEX_GROUND
        ];
    }

    private function internationalShippingServices()
    {
        return [
            self::USPS_PRIORITY_INTERNATIONAL, 
            self::USPS_FIRSTCLASS_INTERNATIONAL,
        ];
    }

    private function colombiaShippingServices()
    {
        return [
            self::COLOMBIA_Standard,
        ];
    }

    private function uspsShippingServices()
    {
        return [
            self::USPS_PRIORITY, 
            self::USPS_FIRSTCLASS, 
            self::USPS_PRIORITY_INTERNATIONAL, 
            self::USPS_FIRSTCLASS_INTERNATIONAL,
        ];
    }
}
