<?php

namespace App\Models;

use App\Models\User;
use App\Models\Region;
use App\Models\ProfitPackage;
use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Services\Calculators\RatesCalculator;
use App\Services\Calculators\WeightCalculator; 
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
    const Brazil_Redispatch = 100;
    const GePS = 537;
    const GePS_EFormat = 540;
    const Prime5 = 773;
    const HoundExpress = 887;
    const USPS_GROUND = 05;
    const Post_Plus_Registered = 734;
    const Post_Plus_EMS = 367;
    const Parcel_Post = 541;
    const Post_Plus_Prime = 777;
    const Post_Plus_Premium = 778;
    const Prime5RIO = 357;
    const DirectLinkCanada = 779; 
    const DirectLinkMexico = 780;
    const DirectLinkChile = 781;
    const DirectLinkAustralia = 782;
    const GDE_PRIORITY_MAIL = 4387;
    const GDE_FIRST_CLASS = 4388;
    const GSS_PMI = 477;
    const GSS_EPMEI = 37634;
    const GSS_EPMI = 3674;
    const GSS_FCM = 3326;
    const GSS_EMS = 4367;
    const TOTAL_EXPRESS = 283;
    const HD_Express = 33173;
    const LT_PRIME = 776;

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
        if($this->isGDEService() && $order){
            return $this->getGDERate($order);
        }
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

    public function isGePSService()
    {
        if (collect($this->gepsShippingServices())->contains($this->service_sub_class)) {
            return true;
        }

        return false;
    }
    public function getIsTotalExpressAttribute()
    {
        return $this->service_sub_class == self::TOTAL_EXPRESS;
    }
    public function isSwedenPostService()
    {
        return in_array($this->service_sub_class,[self::Prime5,self::Prime5RIO,self::DirectLinkCanada,self::DirectLinkMexico,self::DirectLinkChile,self::DirectLinkAustralia]);
    }

    function getIsHoundExpressAttribute(){
        return in_array($this->service_sub_class,[self::HoundExpress]); 
    }

    function getIsDirectlinkCountryAttribute(){
        return in_array($this->service_sub_class,[self::DirectLinkCanada,self::DirectLinkMexico,self::DirectLinkChile,self::DirectLinkAustralia]);
    }

    public function isPostPlusService()
    {
        if($this->service_sub_class == self::Post_Plus_Registered|| $this->service_sub_class == self::Post_Plus_EMS || $this->service_sub_class == self::Post_Plus_Prime || $this->service_sub_class == self::Post_Plus_Premium || $this->service_sub_class == self::LT_PRIME){
            return true;
        }
        return false;
    }

    public function isGDEService()
    {
        if(in_array($this->service_sub_class, [self::GDE_PRIORITY_MAIL, self::GDE_FIRST_CLASS])){
            return true;
        }
        return false;
    }

    public function isHDExpressService()
    {
        if($this->service_sub_class == ShippingService::HD_Express){
            return true;
        }
        return false;
    }

    public function isInboundDomesticService()
    {
        if (collect($this->inboundDomesticShippingServices())->contains($this->service_sub_class)) {
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

    public function isGSSService()
    {
        if($this->service_sub_class == self::GSS_PMI || $this->service_sub_class == self::GSS_EPMEI || $this->service_sub_class == self::GSS_EPMI || $this->service_sub_class == self::GSS_FCM || $this->service_sub_class == self::GSS_EMS){
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

    private function gepsShippingServices()
    {
        return [
            self::GePS,
            self::GePS_EFormat,
            self::Parcel_Post,
        ];
    }

    private function inboundDomesticShippingServices()
    {
        return [
            self::GDE_PRIORITY_MAIL,
            self::GDE_FIRST_CLASS,
        ];
    }

    public function getIsMilliExpressAttribute()
    { 
        return $this->service_sub_class == ShippingService::HD_Express;
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
        if($this->service_sub_class == self::Prime5 || $this->service_sub_class == self::Prime5RIO){
            return true;
        }
        return false;
    }
    public function getIsUspsGroundAttribute()
    { 
        return $this->service_sub_class == ShippingService::USPS_GROUND;
    }
    public function getIsGdePriorityAttribute()
    { 
        return $this->service_sub_class == ShippingService::GDE_PRIORITY_MAIL;
    }
    public function getIsGdeFirstClassAttribute()
    { 
        return $this->service_sub_class == ShippingService::GDE_FIRST_CLASS;
    }
    function getIsBrazilRedispatchAttribute() {
        return $this->service_sub_class == ShippingService::Brazil_Redispatch;
    }

    public function getGDERate($order){
        $zone = getUSAZone($order->recipient->state->code);
        $region = Region::where('country_id', $order->recipient->country_id)->where('code', $zone)->first();
        $weight = ceil(WeightCalculator::kgToGrams($order->weight));
        if ( $weight<100 ){
            $weight = 100;
        }
        $serviceRates = optional(optional($region)->rates())->first();
        if($serviceRates){
            $rate = collect($serviceRates->data)->where('weight','<=',$weight)->sortByDesc('weight')->take(1)->first();
            if(($rate)['leve'] && setting('gde', null, User::ROLE_ADMIN) && setting('gde', null, $order->user_id)){
                $type = 'gde_fc_profit';
                if($this->service_sub_class == self::GDE_PRIORITY_MAIL){
                    $type = 'gde_pm_profit';
                }
                $userProfit = setting($type, null, $order->user_id);
                $adminProfit = setting($type, null, User::ROLE_ADMIN);
                $profit = $userProfit ? $userProfit : $adminProfit; 
                $rate = ($profit / 100) * $rate['leve'] + $rate['leve'];
                return number_format($rate,2);
            }
        }
        return 0;
    }
    function getUspsServiceSubClassAttribute() {
        return in_array($this->service_sub_class,[self::USPS_PRIORITY ,self::USPS_FIRSTCLASS,self::USPS_PRIORITY_INTERNATIONAL,self::USPS_FIRSTCLASS_INTERNATIONAL,self::USPS_GROUND,self::GDE_PRIORITY_MAIL,self::GDE_FIRST_CLASS]);
    }
    function getUpsServiceSubClassAttribute() {
        return $this->service_sub_class == self::UPS_GROUND;        
    }
    function getFedexServiceSubClassAttribute() {
        return $this->service_sub_class == self::FEDEX_GROUND;
    }
    function getGepsServiceSubClassAttribute() {
       return $this->service_sub_class == self::GePS;
    }
    function getGssServiceSubClassAttribute() {
        return $this->service_sub_class == self::GSS_PMI;
    }
    function getSwedenPostServiceSubClassAttribute() {
        return in_array($this->service_sub_class,[self::Prime5,self::Prime5RIO,self::DirectLinkCanada,self::DirectLinkMexico,self::DirectLinkChile,self::DirectLinkAustralia]);        
    }
}
