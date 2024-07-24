<?php

namespace App\Models\Warehouse;

use App\Models\User;
use App\Models\Order;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\Warehouse\Container\PackageResource;
use App\Services\Correios\GetZipcodeGroup;

class Container extends Model implements \App\Services\Correios\Contracts\Container
{
    use SoftDeletes;

    protected $guarded = [];
    
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    const CONTAINER_ANJUN_NX = 'AJ-NX';
    const CONTAINER_ANJUN_IX = 'AJ-IX';
    const CONTAINER_BCN_NX = 'BCN-NX';
    const CONTAINER_BCN_IX = 'BCN-IX';
    const CONTAINER_ANJUNC_NX = 'AJC-NX';
    const CONTAINER_ANJUNC_IX = 'AJC-IX';
     

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRegistered(Builder $builder)
    {
        return $builder->whereNotNull('unit_code');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function deliveryBills()
    {
        return $this->belongsToMany(DeliveryBill::class);
    }

    public function getOrdersCollections()
    {
        return PackageResource::collection($this->orders);
    }

    public function getContainerType()
    {
        return $this->unit_type == 1 ? 'Bag' : 'Box';
    }

    public function getServiceSubClass()
    {
        if($this->services_subclass_code == 'NX'){
            return  'Packet Standard service';
        }elseif($this->services_subclass_code == 'IX'){
            return 'Packet Express service';
        }elseif($this->services_subclass_code == 'XP'){
            return 'Packet Mini service';
        }elseif($this->services_subclass_code == 'SL-NX'){
            return 'SL Standard Modal';
        }elseif($this->services_subclass_code == 'SL-IX'){
            return 'SL Express Modal';
        }elseif($this->services_subclass_code == 'SL-XP'){
            return 'SL Small Parcels';
        }elseif ($this->services_subclass_code == 'AJ-NX') {
            return 'AJ Packet Standard Service';
        }elseif ($this->services_subclass_code == 'AJC-NX') {
            return 'AJC Packet Standard Service';
        }elseif ($this->services_subclass_code == 'AJC-IX') {
            return 'AJC Packet Express Service';
        }elseif ($this->services_subclass_code == 'AJ-IX') {
            return 'AJ Packet Express service';
        }elseif ($this->services_subclass_code == 'BCN-NX') {
            return 'BCN Standard service';
        }elseif ($this->services_subclass_code == 'BCN-IX') {
            return 'BCN Express service';
            return 'AJ Packet Express Service';
        }elseif($this->services_subclass_code == 'SRM'){
            return 'SRM service';
        }elseif($this->services_subclass_code == 'SRP'){
            return 'SRP service';
        }elseif($this->services_subclass_code == 'Priority'){
            return 'Priority';
        }elseif($this->services_subclass_code == '537'){
            return 'Global eParcel Prime';
        }elseif($this->services_subclass_code == '773'){
            return 'Prime5';
        }elseif($this->services_subclass_code == 'USPS Ground'){
            return 'USPS Ground';
        }elseif($this->services_subclass_code == '734'){
            return 'Post Plus';
        }elseif($this->services_subclass_code == '357'){
            return 'Prime5RIO';
        }elseif($this->services_subclass_code == ShippingService::GDE_PRIORITY_MAIL){
            return 'GDE Priority Mail';
        }elseif($this->services_subclass_code == ShippingService::GDE_FIRST_CLASS){
            return 'GDE First Class';
        }elseif($this->services_subclass_code == ShippingService::GSS_PMI){
            return 'Priority Mail International';
        }elseif($this->services_subclass_code == ShippingService::GSS_EPMEI){
            return 'Priority Mail Express International (Pre-Sort)';
        }elseif($this->services_subclass_code == ShippingService::GSS_EPMI){
            return 'Priority Mail International (Pre-Sort)';
        }elseif($this->services_subclass_code == ShippingService::GSS_FCM){
            return 'First Class Package International';
        }elseif($this->services_subclass_code == ShippingService::GSS_EMS){
            return 'Priority Mail Express International (Nationwide)';
        }elseif($this->services_subclass_code == ShippingService::TOTAL_EXPRESS || $this->services_subclass_code == ShippingService::TOTAL_EXPRESS_10KG){
            return 'Total Express';
        }elseif($this->services_subclass_code == ShippingService::DirectLinkAustralia){
            return 'DirectLink Australia';
        }elseif($this->services_subclass_code == ShippingService::DirectLinkCanada){
            return 'DirectLink Canada';
        }elseif($this->services_subclass_code == ShippingService::DirectLinkMexico){
            return 'DirectLink Mexico';
        }elseif($this->services_subclass_code == ShippingService::DirectLinkChile){
            return 'DirectLink Chile';
        }elseif($this->services_subclass_code == ShippingService::GSS_CEP){
            return 'GSS Commercial E-Packet';
        }elseif($this->services_subclass_code == ShippingService::DSS_SENEGAL){
            return 'DSS Senegal';
        }else {
            return 'FirstClass';
        }
    }

    public function getServiceCode()
    {
        if($this->services_subclass_code == 'NX'){
            return  2;
        }elseif($this->services_subclass_code == 'IX'){
            return 1;
        }elseif($this->services_subclass_code == 'XP'){
            return 3;
        }elseif($this->services_subclass_code == 'SRM') {
            return 4;
        }elseif($this->services_subclass_code == 'SRP') {
            return 5;
        }elseif($this->services_subclass_code == 'Priority') {
            return 6;
        }elseif($this->services_subclass_code == 'FirstClass'){
            return 7;
        }elseif($this->services_subclass_code == 'AJ-NX') {
            return 8;
        }elseif($this->services_subclass_code == 'AJ-IX'){
            return 9;
        }elseif($this->services_subclass_code == '537'){
            return 10;
        }
        elseif($this->services_subclass_code == '773'){
            return 11;
        }
        elseif($this->services_subclass_code == '05'){
            return 12;
        }
        elseif($this->services_subclass_code == '734'){
            return 13;
        }
        elseif( $this->services_subclass_code == ShippingService::GDE_PRIORITY_MAIL) {
            return 14;
        }
        elseif( $this->services_subclass_code == ShippingService::GDE_FIRST_CLASS) {
            return 15;
        }elseif( $this->services_subclass_code == ShippingService::TOTAL_EXPRESS || $this->services_subclass_code == ShippingService::TOTAL_EXPRESS_10KG) {
            return 16;
        }
        elseif($this->services_subclass_code == ShippingService::HD_Express){
            return 17;
        }
        elseif( $this->services_subclass_code == 'AJC-IX') {
            return 18;
        }
        elseif( $this->services_subclass_code == 'AJC-NX') {
            return 19;
        }elseif( $this->services_subclass_code == 'BCN-NX') {
            return 20;
        }
        elseif($this->services_subclass_code == 'BCN-IX'){
            return 21;
        }
        elseif($this->services_subclass_code == ShippingService::HoundExpress){
            return 22;
        }
        elseif($this->services_subclass_code == ShippingService::DSS_SENEGAL){
            return 23;
        }
        // return $this->services_subclass_code == 'NX' ? 2 : 1;
    }

    public function getDestinationAriport()
    {
        if($this->destination_operator_name == 'SAOD'){
            return 'GRU';
        }elseif($this->destination_operator_name == 'CRBA') {
            return 'CWB';
        }elseif($this->destination_operator_name == 'MIA') {
            return 'Miami';
        }elseif($this->destination_operator_name == 'MR') {
            return 'Santiago';
        }else {
            return 'Other Region';
        }
        // return $this->destination_operator_name == 'SAOD' ? 'GRU' : 'CWB';
    }

    public function getWeight(): float
    {
        return round($this->orders()->sum(DB::raw('CASE WHEN orders.measurement_unit = "kg/cm" THEN orders.weight ELSE ROUND((orders.weight/2.205), 2) END')),2);
    }

    public function getPiecesCount(): int
    {
        return $this->orders()->count();
    }

    public function getUnitCode()
    {
        return $this->unit_code;
    }

    public function isRegistered()
    {
        return $this->unit_code;
    }

    public function isShipped()
    {
        return $this->deliveryBills()->count() > 0;
    }

    public function getSubClassCode()
    {
        if(in_array($this->services_subclass_code, ['AJ-NX' , 'BCN-NX','AJC-NX'])){
            return 'NX';
        }
        if(in_array($this->services_subclass_code , ['AJ-IX', 'BCN-IX','AJC-IX'])){
            return 'IX';
        }
        return $this->services_subclass_code;
    }

    public function hasAnjunService()
    {
        return in_array($this->services_subclass_code ,['AJ-NX', 'AJ-IX']);
    } 
    public function hasBCNService()
    {
        return in_array($this->services_subclass_code ,['BCN-NX', 'BCN-IX']);
    }
    public function hasBCNStandardService()
    {
        return in_array($this->services_subclass_code ,['BCN-NX']);
    }
    public function hasBCNExpressService()
    {
        return in_array($this->services_subclass_code ,['BCN-IX']);
        return $this->services_subclass_code == 'AJ-NX' || $this->services_subclass_code == 'AJ-IX';
    } 
    public function hasAnjunChinaService()
    {  
        return in_array($this->services_subclass_code ,['AJC-NX','AJC-IX']);
    } 
    public function hasAnjunChinaStandardService()
    {  
        return $this->services_subclass_code == 'AJC-NX';
    } 
    public function hasAnjunChinaExpressService()
    {  
        return $this->services_subclass_code == 'AJC-IX';
    }
    public function hasOrders()
    {
        return $this->orders->isNotEmpty();
    }

    public function hasGePSService()
    {
        return $this->services_subclass_code == ShippingService::GePS || $this->services_subclass_code == ShippingService::GePS_EFormat || $this->services_subclass_code == ShippingService::Parcel_Post;
    }

    public function hasSwedenPostService()
    {
        return in_array($this->services_subclass_code,[ShippingService::Prime5,ShippingService::Prime5RIO,ShippingService::DirectLinkCanada,ShippingService::DirectLinkMexico,ShippingService::DirectLinkChile,ShippingService::DirectLinkAustralia]);
    }
    function getIsDirectlinkCountryAttribute(){
        return in_array($this->services_subclass_code,[ShippingService::DirectLinkCanada,ShippingService::DirectLinkMexico,ShippingService::DirectLinkChile,ShippingService::DirectLinkAustralia]);
    }

    public function hasPostPlusService()
    {
        return $this->services_subclass_code == ShippingService::Post_Plus_Registered || $this->services_subclass_code == ShippingService::Post_Plus_EMS || $this->services_subclass_code == ShippingService::Post_Plus_Prime || $this->services_subclass_code == ShippingService::Post_Plus_Premium;
    }

    public function hasGDEService()
    {
        return $this->services_subclass_code == ShippingService::GDE_PRIORITY_MAIL || $this->services_subclass_code == ShippingService::GDE_FIRST_CLASS;
    }

    public function hasGSSService()
    {
        return $this->services_subclass_code == ShippingService::GSS_PMI || $this->services_subclass_code == ShippingService::GSS_EPMEI || $this->services_subclass_code == ShippingService::GSS_EPMI || $this->services_subclass_code == ShippingService::GSS_FCM || $this->services_subclass_code == ShippingService::GSS_EMS || $this->services_subclass_code == ShippingService::GSS_CEP;
    }

    public function getHasTotalExpressServiceAttribute()
    {
        return $this->services_subclass_code == ShippingService::TOTAL_EXPRESS || $this->services_subclass_code == ShippingService::TOTAL_EXPRESS_10KG;
    }
    public function getHasHoundExpressAttribute()
    {
        return $this->services_subclass_code == ShippingService::HoundExpress;
    }

    public function hasHDExpressService()
    {
        return $this->services_subclass_code == ShippingService::HD_Express;
    }

    public function getGroup($container) {
        
        $firstOrder = $container->orders->first();
        return (new GetZipcodeGroup($firstOrder->recipient->zipcode))->getZipcodeGroup();
    }

    public function hasSenegalService()
    {
        return $this->services_subclass_code == ShippingService::DSS_SENEGAL;
    }

    public function getCustomType()
    {
        return ($this->custom_type == 1 || is_null($this->custom_type) || $this->custom_type === '') ? 'Non-PRC' : 'PRC';
    }

    public function isPRC()
    {
        return $this->custom_type == 2;
    }

}
