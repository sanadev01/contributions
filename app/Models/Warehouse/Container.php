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
        }elseif($this->services_subclass_code == ShippingService::TOTAL_EXPRESS){
            return 'Total Express';
        }elseif($this->services_subclass_code == ShippingService::DirectLinkAustralia){
            return 'DirectLink Australia';
        }elseif($this->services_subclass_code == ShippingService::DirectLinkCanada){
            return 'DirectLink Canada';
        }elseif($this->services_subclass_code == ShippingService::DirectLinkMexico){
            return 'DirectLink Mexico';
        }elseif($this->services_subclass_code == ShippingService::DirectLinkChile){
            return 'DirectLink Chile';
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
        }elseif( $this->services_subclass_code == ShippingService::TOTAL_EXPRESS) {
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
        return $this->services_subclass_code == ShippingService::GSS_PMI || $this->services_subclass_code == ShippingService::GSS_EPMEI || $this->services_subclass_code == ShippingService::GSS_EPMI || $this->services_subclass_code == ShippingService::GSS_FCM || $this->services_subclass_code == ShippingService::GSS_EMS;
    }

    public function getHasTotalExpressServiceAttribute()
    {
        return $this->services_subclass_code == ShippingService::TOTAL_EXPRESS;
    }
    public function getHasHoundExpressAttribute()
    {
        return $this->services_subclass_code == ShippingService::HoundExpress;
    }

    public function hasHDExpressService()
    {
        return $this->services_subclass_code == ShippingService::HD_Express;
    }

    public function getOrderGroupRange($order)
    {
        if ($order) {
            $orderZipcode = $order->recipient->zipcode;

            $groupRanges = [
                ['start' => 1000000, 'end' => 11599999, 'group' => 1],
                ['start' => 11600000, 'end' => 19999999, 'group' => 2],
                ['start' => 20000000, 'end' => 28999999, 'group' => 3],

                ['start' => 30000000, 'end' => 48999999, 'group' => 4],
                ['start' => 49100000, 'end' => 49139999, 'group' => 4],
                ['start' => 49170000, 'end' => 49199999, 'group' => 4],
                ['start' => 49220000, 'end' => 49399999, 'group' => 4],
                ['start' => 49480000, 'end' => 49499999, 'group' => 4],
                ['start' => 49512000, 'end' => 49999999, 'group' => 4],
                ['start' => 50000000, 'end' => 53689999, 'group' => 4],
                ['start' => 53700000, 'end' => 53989999, 'group' => 4],
                ['start' => 54000000, 'end' => 55119999, 'group' => 4],
                ['start' => 55190000, 'end' => 55199999, 'group' => 4],
                ['start' => 55290000, 'end' => 55304999, 'group' => 4],
                ['start' => 55600000, 'end' => 55619999, 'group' => 4],
                ['start' => 56300000, 'end' => 56354999, 'group' => 4],
                ['start' => 57130000, 'end' => 57149999, 'group' => 4],
                ['start' => 57180000, 'end' => 57199999, 'group' => 4],
                ['start' => 57210000, 'end' => 57229999, 'group' => 4],
                ['start' => 57250000, 'end' => 57264999, 'group' => 4],
                ['start' => 57270000, 'end' => 57299999, 'group' => 4],
                ['start' => 57320000, 'end' => 57479999, 'group' => 4],
                ['start' => 57490000, 'end' => 57499999, 'group' => 4],
                ['start' => 57510000, 'end' => 57599999, 'group' => 4],
                ['start' => 57615000, 'end' => 57799999, 'group' => 4],
                ['start' => 57820000, 'end' => 57839999, 'group' => 4],
                ['start' => 57860000, 'end' => 57954999, 'group' => 4],
                ['start' => 57960000, 'end' => 57999999, 'group' => 4],
                ['start' => 58115000, 'end' => 58116999, 'group' => 4],
                ['start' => 58119000, 'end' => 58199999, 'group' => 4],
                ['start' => 58208000, 'end' => 58279999, 'group' => 4],
                ['start' => 58289000, 'end' => 58299999, 'group' => 4],
                ['start' => 58315000, 'end' => 58319999, 'group' => 4],
                ['start' => 58322000, 'end' => 58336999, 'group' => 4],
                ['start' => 58338000, 'end' => 58339999, 'group' => 4],
                ['start' => 58342000, 'end' => 58347999, 'group' => 4],
                ['start' => 58347999, 'end' => 58399999, 'group' => 4],
                ['start' => 58441000, 'end' => 58442999, 'group' => 4],
                ['start' => 58450000, 'end' => 58469999, 'group' => 4],
                ['start' => 58480000, 'end' => 58499999, 'group' => 4],
                ['start' => 58510000, 'end' => 58514999, 'group' => 4],
                ['start' => 58520000, 'end' => 58699999, 'group' => 4],
                ['start' => 58710000, 'end' => 58732999, 'group' => 4],
                ['start' => 58734000, 'end' => 58799999, 'group' => 4],
                ['start' => 58815000, 'end' => 58864999, 'group' => 4],
                ['start' => 58870000, 'end' => 58883999, 'group' => 4],
                ['start' => 58887000, 'end' => 58899999, 'group' => 4],
                ['start' => 58908000, 'end' => 58918999, 'group' => 4],
                ['start' => 58920000, 'end' => 58999999, 'group' => 4],
                ['start' => 59162000, 'end' => 59279999, 'group' => 4],
                ['start' => 59310000, 'end' => 59379999, 'group' => 4],
                ['start' => 59390000, 'end' => 59569999, 'group' => 4],
                ['start' => 59575000, 'end' => 59599999, 'group' => 4],
                ['start' => 59655000, 'end' => 59699999, 'group' => 4],
                ['start' => 59730000, 'end' => 59899999, 'group' => 4],
                ['start' => 59902000, 'end' => 59999999, 'group' => 4],
                ['start' => 60000000, 'end' => 63999999, 'group' => 4],
                ['start' => 65000000, 'end' => 65034999, 'group' => 4],
                ['start' => 65080000, 'end' => 65089999, 'group' => 4],
                ['start' => 70000000, 'end' => 76799999, 'group' => 4],
                ['start' => 76799999, 'end' => 89999999, 'group' => 4],
                ['start' => 90000000, 'end' => 99999999, 'group' => 4],

                ['start' => 29000000, 'end' => 29999999, 'group' => 5],
                ['start' => 49000000, 'end' => 49099999, 'group' => 5],
                ['start' => 49140000, 'end' => 49169999, 'group' => 5],
                ['start' => 49200000, 'end' => 49219999, 'group' => 5],
                ['start' => 49400000, 'end' => 49479999, 'group' => 5],
                ['start' => 49500000, 'end' => 49511999, 'group' => 5],
                ['start' => 53690000, 'end' => 53699999, 'group' => 5],
                ['start' => 53990000, 'end' => 53999999, 'group' => 5],
                ['start' => 55120000, 'end' => 55189999, 'group' => 5],
                ['start' => 55200000, 'end' => 55289999, 'group' => 5],
                ['start' => 55305000, 'end' => 55599999, 'group' => 5],
                ['start' => 55620000, 'end' => 56299999, 'group' => 5],
                ['start' => 56355000, 'end' => 57129999, 'group' => 5],
                ['start' => 57150000, 'end' => 57179999, 'group' => 5],
                ['start' => 57200000, 'end' => 57209999, 'group' => 5],
                ['start' => 57230000, 'end' => 57249999, 'group' => 5],
                ['start' => 57265000, 'end' => 57269999, 'group' => 5],
                ['start' => 57300000, 'end' => 57319999, 'group' => 5],
                ['start' => 57480000, 'end' => 57489999, 'group' => 5],
                ['start' => 57500000, 'end' => 57509999, 'group' => 5],
                ['start' => 57600000, 'end' => 57614999, 'group' => 5],
                ['start' => 57800000, 'end' => 57819999, 'group' => 5],
                ['start' => 57840000, 'end' => 57859999, 'group' => 5],
                ['start' => 57955000, 'end' => 57959999, 'group' => 5],
                ['start' => 58000000, 'end' => 58114999, 'group' => 5],
                ['start' => 58117000, 'end' => 58118999, 'group' => 5],
                ['start' => 58200000, 'end' => 58207999, 'group' => 5],
                ['start' => 58280000, 'end' => 58288999, 'group' => 5],
                ['start' => 58300000, 'end' => 58314999, 'group' => 5],
                ['start' => 58320000, 'end' => 58321999, 'group' => 5],
                ['start' => 58337000, 'end' => 58337999, 'group' => 5],
                ['start' => 58340000, 'end' => 58341999, 'group' => 5],
                ['start' => 58348000, 'end' => 58349999, 'group' => 5],
                ['start' => 58400000, 'end' => 58440999, 'group' => 5],
                ['start' => 58443000, 'end' => 58449999, 'group' => 5],
                ['start' => 58470000, 'end' => 58479999, 'group' => 5],
                ['start' => 58500000, 'end' => 58509999, 'group' => 5],
                ['start' => 58515000, 'end' => 58519999, 'group' => 5],
                ['start' => 58700000, 'end' => 58700000, 'group' => 5],
                ['start' => 58733000, 'end' => 58733999, 'group' => 5],
                ['start' => 58800000, 'end' => 58814999, 'group' => 5],
                ['start' => 58865000, 'end' => 58869999, 'group' => 5],
                ['start' => 58884000, 'end' => 58886999, 'group' => 5],
                ['start' => 58900000, 'end' => 58907999, 'group' => 5],
                ['start' => 58919000, 'end' => 58919999, 'group' => 5],
                ['start' => 59000000, 'end' => 59161999, 'group' => 5],
                ['start' => 59280000, 'end' => 59309999, 'group' => 5],
                ['start' => 59380000, 'end' => 59389999, 'group' => 5],
                ['start' => 59570000, 'end' => 59574999, 'group' => 5],
                ['start' => 59600000, 'end' => 59654999, 'group' => 5],
                ['start' => 59700000, 'end' => 59729999, 'group' => 5],
                ['start' => 59900000, 'end' => 59901999, 'group' => 5],
                ['start' => 64000000, 'end' => 64999999, 'group' => 5],
                ['start' => 65035000, 'end' => 65079999, 'group' => 5],
                ['start' => 65090000, 'end' => 69999999, 'group' => 5],
                ['start' => 76800000, 'end' => 79999999, 'group' => 5],
            ];

             // Sort the groupRanges array based on the 'start' key
            usort($groupRanges, function ($a, $b) {
                return $a['start'] - $b['start'];
            });

            foreach ($groupRanges as $range) {
                if ($orderZipcode >= $range['start'] && $orderZipcode <= $range['end']) {
                    return $range;
                } elseif ($orderZipcode < $range['start']) {
                    // Break out of the loop if the current range's start is greater than the order's zipcode
                    break;
                }
            }
        }

        return null;
    }

    public function getGroup($container) {
        
        $containerOrder = $container->orders->first();
        $firstOrderGroupRange = $this->getOrderGroupRange($containerOrder);
        
        return $firstOrderGroupRange['group'];
    }
}
