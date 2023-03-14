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
    const AJ_Standard_CN = 34166;
    const AJ_Express_CN = 33174;
    const PostNL = 87765;
    const AJ_Packet_Standard = 33164;
    const AJ_Packet_Express = 33172;
    const Mile_Express = 33173;
    const COLOMBIA_URBANO = 44162;
    const COLOMBIA_NACIONAL = 44163;
    const COLOMBIA_TRAYETOS = 44164;
    const Brazil_Redispatch = 100;
    const GePS = 537;
    const GePS_EFormat = 540;
    const Prime5 = 773;
    const Post_Plus_Registered = 734;
    const Post_Plus_EMS = 367;
    const USPS_GROUND = 05;
    const Parcel_Post = 541;

    

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
    

    public function getCalculator(Order $order, $calculateOnVolumeMetricWeight = true, $originalRate = false)
    {
        // if ( self::$calculator && $this->cacheCalculator)
        //     return self::$calculator;

        self::$calculator = new RatesCalculator($order,$this, $calculateOnVolumeMetricWeight, $originalRate);

        return self::$calculator;
    }

    public function profitPackages()
    {
        return $this->hasMany(ProfitPackage::class);
    }

    public function getOriginalRate(Order $order, $withProfit = true, $calculateOnVolumeMetricWeight = true, $originalRate = true)
    {
        $rate = round($this->getCalculator($order, $calculateOnVolumeMetricWeight, $originalRate)->getRate($withProfit),2);
        return $rate;
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

    public function isGePSService()
    {
        if (collect($this->gepsShippingServices())->contains($this->service_sub_class)) {
            return true;
        }

        return false;
    }
    public function isSwedenPostService()
    {
        if($this->service_sub_class == self::Prime5){
            return true;
        }
        return false;
    }

    public function isPostPlusService()
    {
        if($this->service_sub_class == self::Post_Plus_Registered|| $this->service_sub_class == self::Post_Plus_EMS){
            return true;
        }
        return false;
    }

    public function isGePSeFormatService()
    {
        if (collect($this->gepsShippingServices())->contains($this->service_sub_class)) {
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

    public function isMileExpressService()
    {
        return $this->service_sub_class == ShippingService::Mile_Express;
    }
    public function isAnjunChinaExpressService()
    {
        return self::AJ_Express_CN  == $this->service_sub_class;
    }
    public function isAnjunChinaStandardService()
    {
        return self::AJ_Standard_CN  == $this->service_sub_class;

    }

    public function isColombiaService()
    {
        if (collect($this->colombiaShippingServices())->contains($this->service_sub_class)) {
            return true;
        }

        return false;
    }

    public function isPostNLService()
    {
        if (collect($this->postNLShippingServices())->contains($this->service_sub_class)) {
            return true;
        }

        return false;
    }

    public function isCorreiosChileService()
    {
        if (collect($this->correiosChileShippingServices())->contains($this->service_sub_class)) {
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
    public function isAnjunChinaService()
    {
        return self::AJ_Standard_CN == $this->service_sub_class || self::AJ_Express_CN == $this->service_sub_class;
    }
    public function isCorreiosService()
    {
        if (collect($this->correiosShippingServices())->contains($this->service_sub_class)) {
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
            self::FEDEX_GROUND,
            self::USPS_GROUND,
        ];
    }

    private function domesticShippingServices()
    {
        return [
            self::USPS_PRIORITY,
            self::USPS_FIRSTCLASS,
            self::UPS_GROUND, 
            self::FEDEX_GROUND,
            self::USPS_GROUND,
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
            self::COLOMBIA_URBANO,
            self::COLOMBIA_NACIONAL,
            self::COLOMBIA_TRAYETOS,
        ];
    }

    private function postNLShippingServices()
    {
        return [
            self::PostNL,
        ];
    }

    private function uspsShippingServices()
    {
        return [
            self::USPS_PRIORITY,
            self::USPS_FIRSTCLASS,
            self::USPS_PRIORITY_INTERNATIONAL,
            self::USPS_FIRSTCLASS_INTERNATIONAL,
            self::USPS_GROUND,
        ];
    }

    private function correiosChileShippingServices()
    {
        return [
            self::SRP,
            self::SRM,
        ];
    }

    private function gepsShippingServices()
    {
        return [
            self::GePS,
            self::GePS_EFormat,
            self::Parcel_Post,
        ];
    }
    public function getIsMilliExpressAttribute()
    { 
        return $this->service_sub_class == ShippingService::Mile_Express;
    }
    public function getIsUspsPriorityAttribute()
    { 
        return $this->service_sub_class == ShippingService::USPS_PRIORITY;
    }
    public function getIsUspsFirstclassAttribute()
    { 
        return $this->service_sub_class == ShippingService::USPS_FIRSTCLASS;
    }

    public function getIsUpsGroundAttribute()
    {  
        return $this->service_sub_class == ShippingService::UPS_GROUND;
    }
    public function getIsFedexGroundAttribute()
    { 
        return $this->service_sub_class == ShippingService::FEDEX_GROUND;
    }
    public function getIsUspsPriorityInternationalAttribute()
    { 
        return $this->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL;
    }
    public function getIsUspsFirstclassInternationalAttribute()
    { 
        return $this->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL;
    }
    public function getIsgepsAttribute()
    {
        if (collect($this->gepsShippingServices())->contains($this->service_sub_class)) {
            return true;
        }

        return false;
    }
    public function getIsSwedenPostAttribute()
    {
        if($this->service_sub_class == self::Prime5){
            return true;
        }
        return false;
    }
    public function getIsUspsGroundAttribute()
    { 
        return $this->service_sub_class == ShippingService::USPS_GROUND;
    }
    
}
